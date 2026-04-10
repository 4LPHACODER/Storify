<?php

namespace App\Services\Customer;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function createOrder(User $user, array $shippingData): Order
    {
        $items = $user->cartItems()->with('product')->get();

        $subtotal = $items->sum(fn ($item) => $item->quantity * (float) $item->product->price);
        $shippingFee = $shippingData['shipping_method'] === 'express' ? 12.00 : 5.00;
        $total = $subtotal + $shippingFee;

        return DB::transaction(function () use ($user, $items, $shippingData, $subtotal, $shippingFee, $total): Order {
            $order = Order::query()->create([
                'user_id' => $user->id,
                'full_name' => $shippingData['full_name'],
                'address' => $shippingData['address'],
                'contact_number' => $shippingData['contact_number'],
                'city' => $shippingData['city'],
                'postal_code' => $shippingData['postal_code'],
                'shipping_method' => $shippingData['shipping_method'],
                'payment_method' => $shippingData['payment_method'],
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'status' => Order::STATUS_PENDING,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'line_total' => $item->quantity * (float) $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            $user->cartItems()->delete();

            return $order;
        });
    }
}
