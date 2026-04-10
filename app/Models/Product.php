<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
        'image_link',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->image && Storage::disk('public')->exists($this->image)) {
                return Storage::url($this->image);
            }

            if ($this->image_link) {
                return $this->image_link;
            }

            return '/images/product-placeholder.svg';
        });
    }
}
