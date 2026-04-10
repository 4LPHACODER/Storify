<?php

namespace App\Services\Admin;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Order::query()
            ->with(['user', 'items'])
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status),
            )
            ->when(
                $filters['customer'] ?? null,
                fn ($query, $customer) => $query->whereHas('user', fn ($subQuery) => $subQuery->where('name', 'like', "%{$customer}%")),
            )
            ->when(
                $filters['date'] ?? null,
                fn ($query, $date) => $query->whereDate('created_at', $date),
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order->fresh(['user', 'items']);
    }
}
