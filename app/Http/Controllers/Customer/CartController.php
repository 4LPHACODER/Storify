<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CartStoreRequest;
use App\Http\Requests\Customer\CartUpdateRequest;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\Customer\CartService;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index(Request $request): Response
    {
        $items = $request->user()
            ->cartItems()
            ->with('product')
            ->get();

        $subtotal = $items->sum(fn (CartItem $item) => $item->quantity * (float) $item->product->price);

        return Inertia::render('Customer/Cart/Index', [
            'items' => $items,
            'subtotal' => number_format($subtotal, 2, '.', ''),
        ]);
    }

    public function store(CartStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $product = Product::query()->findOrFail($validated['product_id']);
        $quantity = $validated['quantity'] ?? 1;
        abort_if($product->stock < 1, 422, 'This product is out of stock.');

        $this->cartService->add($request->user(), $product, $quantity);

        return redirect()->route('customer.cart.index');
    }

    public function update(CartUpdateRequest $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);
        $this->cartService->updateQuantity($cartItem, $request->integer('quantity'));

        return redirect()->route('customer.cart.index');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);
        $this->cartService->remove($cartItem);

        return redirect()->route('customer.cart.index');
    }

    public function buyNow(CartStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $product = Product::query()->findOrFail($validated['product_id']);
        $quantity = $validated['quantity'] ?? 1;
        abort_if($product->stock < 1, 422, 'This product is out of stock.');
        $this->cartService->add($request->user(), $product, $quantity);

        return redirect()->route('customer.checkout.index');
    }
}
