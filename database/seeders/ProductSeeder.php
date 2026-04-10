<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            [
                'name' => 'Wireless Mouse Pro',
                'description' => 'Ergonomic wireless mouse with programmable buttons.',
                'price' => 29.00,
                'stock' => 64,
                'image' => null,
                'image_link' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => 'Gaming Keyboard X',
                'description' => 'Mechanical keyboard with RGB lighting and blue switches.',
                'price' => 89.00,
                'stock' => 8,
                'image' => null,
                'image_link' => 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => '27 inch Monitor 2K',
                'description' => '27-inch 2K IPS monitor suitable for office and gaming.',
                'price' => 249.00,
                'stock' => 24,
                'image' => null,
                'image_link' => 'https://images.unsplash.com/photo-1527443154391-507e9dc6c5cc?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => 'USB-C Hub 8 in 1',
                'description' => 'Multiport USB-C hub with HDMI, ethernet, and SD card support.',
                'price' => 39.00,
                'stock' => 0,
                'image' => null,
                'image_link' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=900&q=80',
            ],
        ])->each(fn (array $product) => Product::query()->updateOrCreate(
            ['name' => $product['name']],
            $product,
        ));
    }
}
