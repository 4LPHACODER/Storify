<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Customer\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Customer/Orders/Index', [
            'statuses' => Order::statuses(),
            'orders' => $this->orderService->paginate($request->user()),
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        return Inertia::render('Customer/Orders/Show', [
            'statuses' => Order::statuses(),
            'order' => $this->orderService->get($request->user(), $order),
        ]);
    }
}
