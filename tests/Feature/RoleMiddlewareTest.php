<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to login for customer routes', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('guests are redirected to login for admin routes', function () {
    $response = $this->get(route('admin.products.index'));

    $response->assertRedirect(route('login'));
});

test('customers can access dashboard', function () {
    $user = User::factory()->customer()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('customers cannot access admin products', function () {
    $user = User::factory()->customer()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('admin.products.index'));

    $response->assertForbidden();
});

test('admins can access admin products', function () {
    $user = User::factory()->admin()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('admin.products.index'));

    $response->assertOk();
});

test('admins can access dashboard', function () {
    $user = User::factory()->admin()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});
