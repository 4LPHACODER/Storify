<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access admin product list', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.products.index'));

    $response->assertOk();
});

test('customer can view customer product list and details', function () {
    $customer = User::factory()->customer()->create();
    $product = Product::query()->create([
        'name' => 'Sample Product',
        'description' => 'Sample description',
        'price' => 20.00,
        'stock' => 12,
        'image' => null,
        'image_link' => null,
    ]);

    $listResponse = $this->actingAs($customer)->get(route('customer.products.index'));
    $listResponse->assertOk();

    $detailsResponse = $this->actingAs($customer)->get(route('customer.products.show', $product));
    $detailsResponse->assertOk();
});

test('customer cannot access admin product routes', function () {
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($customer)->get(route('admin.products.index'));

    $response->assertForbidden();
});

test('admin can create and delete product', function () {
    $admin = User::factory()->admin()->create();

    $createResponse = $this->actingAs($admin)->post(route('admin.products.store'), [
        'name' => 'Product A',
        'description' => 'Product A description',
        'price' => 10.50,
        'stock' => 5,
        'image_link' => 'https://example.com/product-a.jpg',
    ]);

    $createResponse->assertRedirect(route('admin.products.index'));
    $this->assertDatabaseHas('products', ['name' => 'Product A']);

    $product = Product::query()->where('name', 'Product A')->firstOrFail();

    $deleteResponse = $this->actingAs($admin)->delete(route('admin.products.destroy', $product));
    $deleteResponse->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('admin cannot access customer only product routes', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('customer.products.index'));

    $response->assertForbidden();
});
