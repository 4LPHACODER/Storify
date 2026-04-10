<?php

namespace App\Services\Customer;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function paginate(User $user): LengthAwarePaginator
    {
        return $user->orders()
            ->with('items')
            ->latest()
            ->paginate(10)
            ->through(function (Order $order): array {
                $firstItem = $order->items->first();
                $itemsCount = $order->items->count();

                $title = $firstItem?->name ?? 'Order items';

                if ($itemsCount > 1) {
                    $remainingItems = $itemsCount - 1;
                    $title .= " and {$remainingItems} more item".($remainingItems > 1 ? 's' : '');
                }

                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total' => (string) $order->total,
                    'order_title' => $title,
                    'created_at_human' => Carbon::parse($order->created_at)->format('M d, Y h:i A'),
                    'created_at' => $order->created_at,
                ];
            });
    }

    public function get(User $user, Order $order): Order
    {
        abort_unless($order->user_id === $user->id, 403);

        return $order->load('items');
    }
}
