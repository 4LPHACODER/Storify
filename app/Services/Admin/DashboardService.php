<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function summary(): array
    {
        $totalProducts = Product::query()->count();
        $totalOrders = Order::query()->count();
        $pendingOrders = Order::query()->where('status', Order::STATUS_PENDING)->count();
        $deliveredOrders = Order::query()->where('status', Order::STATUS_DELIVERED)->count();
        $totalCustomers = User::query()->where('role', 'customer')->count();
        $revenue = (float) Order::query()->where('status', '!=', Order::STATUS_CANCELLED)->sum('total');
        $lowStockProducts = Product::query()->where('stock', '<=', 10)->orderBy('stock')->limit(5)->get();
        $ordersByStatus = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $salesOverview = Order::query()
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $periodStart = now()->startOfMonth()->subMonths(5);
        $periodEnd = now()->endOfMonth();
        $monthlyRows = Order::query()
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_key, SUM(total) as total_revenue')
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $monthlyRevenueTrend = collect(CarbonPeriod::create($periodStart, '1 month', $periodEnd))
            ->map(function (Carbon $date) use ($monthlyRows): array {
                $key = $date->format('Y-m');
                $row = $monthlyRows->get($key);

                return [
                    'month' => $date->format('M Y'),
                    'revenue' => (float) ($row->total_revenue ?? 0),
                ];
            })
            ->values();

        $bestSellingProducts = OrderItem::query()
            ->selectRaw('name, SUM(quantity) as total_quantity')
            ->groupBy('name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'deliveredOrders' => $deliveredOrders,
            'totalCustomers' => $totalCustomers,
            'revenue' => number_format($revenue, 2, '.', ''),
            'lowStockProducts' => $lowStockProducts,
            'ordersByStatus' => $ordersByStatus,
            'salesOverview' => $salesOverview,
            'monthlyRevenueTrend' => $monthlyRevenueTrend,
            'bestSellingProducts' => $bestSellingProducts,
        ];
    }
}
