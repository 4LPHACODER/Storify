<?php

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer can add product to cart', function () {
    $customer = User::factory()->customer()->create();
    $product = Product::query()->create([
        'name' => 'Test Product',
        'description' => 'Test product description',
        'price' => 12.50,
        'stock' => 10,
        'image' => null,
        'image_link' => null,
    ]);

    $response = $this->actingAs($customer)->post(route('customer.cart.store'), [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertRedirect(route('customer.cart.index'));
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('customer can update and remove cart item', function () {
    $customer = User::factory()->customer()->create();
    $product = Product::query()->create([
        'name' => 'Test Product 2',
        'description' => 'Test product description',
        'price' => 16.00,
        'stock' => 20,
        'image' => null,
        'image_link' => null,
    ]);

    $cartItem = CartItem::query()->create([
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $updateResponse = $this->actingAs($customer)->patch(route('customer.cart.update', $cartItem), [
        'quantity' => 3,
    ]);
    $updateResponse->assertRedirect(route('customer.cart.index'));
    $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id, 'quantity' => 3]);

    $deleteResponse = $this->actingAs($customer)->delete(route('customer.cart.destroy', $cartItem));
    $deleteResponse->assertRedirect(route('customer.cart.index'));
    $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
});

test('customer can place order from checkout', function () {
    $customer = User::factory()->customer()->create();
    $product = Product::query()->create([
        'name' => 'Checkout Product',
        'description' => 'Checkout product description',
        'price' => 25.00,
        'stock' => 5,
        'image' => null,
        'image_link' => null,
    ]);

    CartItem::query()->create([
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($customer)->post(route('customer.checkout.store'), [
        'full_name' => 'John Customer',
        'address' => '123 Main Street',
        'contact_number' => '09123456789',
        'city' => 'Jakarta',
        'postal_code' => '12345',
        'shipping_method' => 'standard',
        'payment_method' => 'cod',
    ]);

    $response->assertRedirect(route('customer.products.index'));
    expect(Order::query()->count())->toBe(1);
    $this->assertDatabaseCount('order_items', 1);
    $this->assertDatabaseMissing('cart_items', ['user_id' => $customer->id]);
});
