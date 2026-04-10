<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        $products = Product::query()
            ->when(
                $search,
                fn ($query) => $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                }),
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Customer/Products/Index', [
            'products' => $products,
            'filters' => $request->only('search'),
        ]);
    }

    public function show(Product $product): Response
    {
        return Inertia::render('Customer/Products/Show', [
            'product' => $product,
        ]);
    }
}
