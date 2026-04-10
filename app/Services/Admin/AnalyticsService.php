<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function data(): array
    {
        $totalSales = (float) Order::query()->where('status', '!=', Order::STATUS_CANCELLED)->sum('total');
        $totalOrders = Order::query()->count();

        $ordersByStatus = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $periodStart = now()->startOfMonth()->subMonths(5);
        $periodEnd = now()->endOfMonth();

        $monthlyRows = Order::query()
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_key, COUNT(*) as total_orders, SUM(total) as total_revenue')
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $monthlyTrend = collect(CarbonPeriod::create($periodStart, '1 month', $periodEnd))
            ->map(function (Carbon $date) use ($monthlyRows): array {
                $key = $date->format('Y-m');
                $row = $monthlyRows->get($key);

                return [
                    'month_key' => $key,
                    'month' => $date->format('M Y'),
                    'orders' => (int) ($row->total_orders ?? 0),
                    'revenue' => (float) ($row->total_revenue ?? 0),
                ];
            })
            ->values();

        $salesOverview = Order::query()
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $bestSellingProducts = OrderItem::query()
            ->selectRaw('name, SUM(quantity) as total_quantity')
            ->groupBy('name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $lowStockProducts = Product::query()
            ->where('stock', '<=', 10)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $recentOrders = Order::query()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        return [
            'totalSales' => number_format($totalSales, 2, '.', ''),
            'totalOrders' => $totalOrders,
            'ordersByStatus' => $ordersByStatus,
            'monthlyTrend' => $monthlyTrend,
            'salesOverview' => $salesOverview,
            'bestSellingProducts' => $bestSellingProducts,
            'lowStockProducts' => $lowStockProducts,
            'recentOrders' => $recentOrders,
        ];
    }
}
