<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CheckoutStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\Customer\CheckoutService;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(private readonly CheckoutService $checkoutService) {}

    public function index(Request $request): Response|RedirectResponse
    {
        $items = $request->user()
            ->cartItems()
            ->with('product')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('customer.cart.index');
        }

        $subtotal = $items->sum(fn ($item) => $item->quantity * (float) $item->product->price);
        $shippingMethods = [
            ['value' => 'standard', 'label' => 'Standard Shipping', 'fee' => 5.00],
            ['value' => 'express', 'label' => 'Express Shipping', 'fee' => 12.00],
        ];

        return Inertia::render('Customer/Checkout/Index', [
            'items' => $items,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'shippingMethods' => $shippingMethods,
        ]);
    }

    public function store(CheckoutStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $items = $request->user()->cartItems()->with('product')->get();

        abort_if($items->isEmpty(), 422, 'Cart is empty.');
        abort_if(
            $items->contains(fn ($item) => $item->quantity > $item->product->stock),
            422,
            'One or more items are out of stock.',
        );
        $this->checkoutService->createOrder($request->user(), $validated);

        return redirect()->route('customer.products.index');
    }
}
