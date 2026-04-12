<?php

namespace App\Services\Customer;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function paginate(User $user, string $filter = 'all'): LengthAwarePaginator
    {
        return $user->orders()
            ->with('items.product')
            ->when(
                $filter !== 'all',
                function ($query) use ($filter) {
                    if ($filter === 'to_deliver') {
                        $query->whereIn('status', [
                            Order::STATUS_PENDING,
                            Order::STATUS_CONFIRMED,
                            Order::STATUS_PACKED,
                            Order::STATUS_SHIPPED,
                            Order::STATUS_OUT_FOR_DELIVERY,
                            Order::STATUS_DELIVERED,
                        ]);
                    }

                    if ($filter === 'received') {
                        $query->where('status', Order::STATUS_RECEIVED);
                    }

                    if ($filter === 'cancelled') {
                        $query->where('status', Order::STATUS_CANCELLED);
                    }
                },
            )
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
                    'created_at_human' => Carbon::parse($order->created_at)->format('M d, Y'),
                    'created_at' => $order->created_at,
                    'items_count' => $itemsCount,
                    'shipping_method' => $order->shipping_method,
                    'delivery_estimate' => $this->resolveDeliveryEstimate($order),
                    'image_url' => $firstItem?->product?->image_url ?? '/images/product-placeholder.svg',
                    'can_mark_received' => in_array($order->status, Order::customerReceivableStatuses(), true),
                    'can_cancel' => in_array($order->status, [
                        Order::STATUS_PENDING,
                        Order::STATUS_CONFIRMED,
                        Order::STATUS_PACKED,
                    ], true),
                ];
            });
    }

    public function get(User $user, Order $order): Order
    {
        abort_unless($order->user_id === $user->id, 403);

        return $order->load('items.product');
    }

    /**
     * @param  array{status:string, rating?:int|string|null, feedback?:string|null}  $data
     */
    public function updateStatus(User $user, Order $order, array $data): Order
    {
        abort_unless($order->user_id === $user->id, 403);

        $status = (string) ($data['status'] ?? '');

        if ($status === Order::STATUS_RECEIVED) {
            if (! in_array($order->status, Order::customerReceivableStatuses(), true)) {
                throw ValidationException::withMessages([
                    'status' => 'This order cannot be marked as received yet.',
                ]);
            }

            $rating = (int) ($data['rating'] ?? 0);

            if ($rating < 1 || $rating > 5) {
                throw ValidationException::withMessages([
                    'rating' => 'Please provide a valid rating between 1 and 5 stars.',
                ]);
            }
        }

        if ($status === Order::STATUS_CANCELLED) {
            if (! in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CONFIRMED, Order::STATUS_PACKED], true)) {
                throw ValidationException::withMessages([
                    'status' => 'This order can no longer be cancelled.',
                ]);
            }
        }

        if ($status === Order::STATUS_RECEIVED) {
            $order->update([
                'status' => $status,
                'received_at' => now(),
                'customer_rating' => (int) $data['rating'],
                'customer_feedback' => filled($data['feedback'] ?? null) ? trim((string) $data['feedback']) : null,
            ]);
        } else {
            $order->update(['status' => $status]);
        }

        return $order->fresh(['items.product']);
    }

    private function resolveDeliveryEstimate(Order $order): string
    {
        if ($order->estimated_delivery_date) {
            return Carbon::parse($order->estimated_delivery_date)->format('M d, Y');
        }

        return $order->delivery_estimate_label ?? 'Not set';
    }
}
