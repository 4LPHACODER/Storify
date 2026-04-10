<?php

namespace App\Services\Customer;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    public function summary(User $user): array
    {
        $ordersQuery = $user->orders();

        return [
            'totalOrders' => (clone $ordersQuery)->count(),
            'pendingOrders' => (clone $ordersQuery)->where('status', Order::STATUS_PENDING)->count(),
            'deliveredOrders' => (clone $ordersQuery)->where('status', Order::STATUS_DELIVERED)->count(),
            'recentOrders' => (clone $ordersQuery)
                ->with('items')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function (Order $order): array {
                    $firstItem = $order->items->first();
                    $itemsCount = $order->items->count();

                    $title = $firstItem?->name ?? 'Order items';

                    if ($itemsCount > 1) {
                        $title .= ' and '.($itemsCount - 1).' more item'.($itemsCount > 2 ? 's' : '');
                    }

                    return [
                        'id' => $order->id,
                        'status' => $order->status,
                        'total' => (string) $order->total,
                        'order_title' => $title,
                        'created_at_human' => Carbon::parse($order->created_at)->format('M d, Y'),
                    ];
                }),
            'recommendedProducts' => Product::query()
                ->where('stock', '>', 0)
                ->where(function ($query) {
                    $query->whereNotNull('image')->orWhereNotNull('image_link');
                })
                ->latest()
                ->limit(6)
                ->get(),
        ];
    }
}
