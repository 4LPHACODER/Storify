<?php

namespace App\Services\Customer;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardService
{
    public function summary(User $user): array
    {
        $ordersQuery = $user->orders();

        return [
            'totalOrders' => (clone $ordersQuery)->count(),
            'pendingOrders' => (clone $ordersQuery)->where('status', Order::STATUS_PENDING)->count(),
            'deliveredOrders' => (clone $ordersQuery)->where('status', Order::STATUS_DELIVERED)->count(),
            'recentOrders' => (clone $ordersQuery)->with('items')->latest()->limit(5)->get(),
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
