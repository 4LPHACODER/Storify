<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\OrderActionRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use App\Services\Customer\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(Request $request): Response
    {
        $filter = $request->string('filter')->toString() ?: 'all';
        $allowedFilters = ['to_deliver', 'received', 'cancelled', 'all'];

        if (! in_array($filter, $allowedFilters, true)) {
            $filter = 'all';
        }

        return Inertia::render('Customer/Orders/Index', [
            'filters' => [
                'filter' => $filter,
            ],
            'statuses' => Order::statuses(),
            'orders' => $this->orderService->paginate($request->user(), $filter),
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        return Inertia::render('Customer/Orders/Show', [
            'statuses' => Order::statuses(),
            'order' => $this->orderService->get($request->user(), $order),
        ]);
    }

    public function updateStatus(OrderActionRequest $request, Order $order): RedirectResponse
    {
        $this->orderService->updateStatus(
            $request->user(),
            $order,
            $request->string('status')->toString(),
        );

        return redirect()->back();
    }
}
