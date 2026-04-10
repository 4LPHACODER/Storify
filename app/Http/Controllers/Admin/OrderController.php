<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusUpdateRequest;
use App\Models\Order;
use App\Services\Admin\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'date', 'customer']);

        return Inertia::render('Admin/Orders/Index', [
            'filters' => $filters,
            'statuses' => Order::statuses(),
            'orders' => $this->orderService->paginate($filters),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show(Order $order): Response
    {
        return Inertia::render('Admin/Orders/Show', [
            'order' => $order->load(['user', 'items.product']),
            'statuses' => Order::statuses(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderStatusUpdateRequest $request, Order $order): RedirectResponse
    {
        $this->orderService->updateStatus($order, $request->string('status')->toString());

        return redirect()->route('admin.orders.show', $order);
    }

}
