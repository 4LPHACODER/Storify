<?php

namespace App\Services\Customer;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

class CartService
{
    public function add(User $user, Product $product, int $quantity = 1): CartItem
    {
        $item = CartItem::query()->firstOrNew([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $item->quantity = min($product->stock, $item->exists ? $item->quantity + $quantity : $quantity);
        $item->save();

        return $item;
    }

    public function updateQuantity(CartItem $item, int $quantity): CartItem
    {
        $item->update([
            'quantity' => min($quantity, $item->product->stock),
        ]);

        return $item;
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }
}
