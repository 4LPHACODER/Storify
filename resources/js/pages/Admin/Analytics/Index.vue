<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    ArcElement,
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from 'chart.js';
import { computed } from 'vue';
import { Bar, Doughnut, Line } from 'vue-chartjs';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type SalesOverviewPoint = { date: string; revenue: string };
type StatusPoint = { status: string; total: number };
type MonthlyPoint = { month: string; orders: number; revenue: number };
type TopProduct = { name: string; total_quantity: number };
type Product = { id: number; name: string; stock: number };
type RecentOrder = { id: number; total: string; status: string; user: { name: string } };

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Tooltip,
    Legend,
);

const props = defineProps<{
    totalSales: string;
    totalOrders: number;
    salesOverview: SalesOverviewPoint[];
    ordersByStatus: StatusPoint[];
    monthlyTrend: MonthlyPoint[];
    bestSellingProducts: TopProduct[];
    lowStockProducts: Product[];
    recentOrders: RecentOrder[];
}>();

const salesOverviewData = computed(() => ({
    labels: props.salesOverview.map((point) => point.date),
    datasets: [
        {
            label: 'Revenue',
            data: props.salesOverview.map((point) => Number(point.revenue)),
            borderColor: '#1ED760',
            backgroundColor: 'rgba(29,185,84,0.24)',
            tension: 0.35,
            fill: true,
        },
    ],
}));

const ordersByStatusData = computed(() => ({
    labels: props.ordersByStatus.map((point) => point.status.replaceAll('_', ' ')),
    datasets: [
        {
            label: 'Orders',
            data: props.ordersByStatus.map((point) => point.total),
            backgroundColor: [
                '#1DB954',
                '#1ED760',
                '#9BF0B2',
                '#B3B3B3',
                '#FFFFFF',
                '#16A34A',
                '#ef4444',
            ],
        },
    ],
}));

const monthlyOrdersData = computed(() => ({
    labels: props.monthlyTrend.map((point) => point.month),
    datasets: [
        {
            label: 'Monthly Orders',
            data: props.monthlyTrend.map((point) => point.orders),
            backgroundColor: '#1DB954',
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: '#e5e7eb',
            },
        },
    },
    scales: {
        x: {
            ticks: { color: '#B3B3B3' },
            grid: { color: 'rgba(42,42,42,0.9)' },
        },
        y: {
            ticks: { color: '#B3B3B3' },
            grid: { color: 'rgba(42,42,42,0.9)' },
        },
    },
} as const;
</script>

<template>
    <Head title="Analytics" />

    <div class="space-y-4 bg-background p-4">
        <h1 class="text-2xl font-semibold">Storify Analytics</h1>

        <div class="grid gap-4 sm:grid-cols-2">
            <Card class="border-border bg-card/95">
                <CardHeader><CardTitle>Total Sales</CardTitle></CardHeader>
                <CardContent class="text-2xl font-semibold">${{ props.totalSales }}</CardContent>
            </Card>
            <Card class="border-border bg-card/95">
                <CardHeader><CardTitle>Total Orders</CardTitle></CardHeader>
                <CardContent class="text-2xl font-semibold">{{ props.totalOrders }}</CardContent>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card class="border-border bg-card/95">
                <CardHeader><CardTitle>Sales Overview</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Line :data="salesOverviewData" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>
            <Card class="border-border bg-card/95">
                <CardHeader><CardTitle>Orders by Status</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Doughnut :data="ordersByStatusData" :options="{ responsive: true, maintainAspectRatio: false }" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card class="border-border bg-card/95">
                <CardHeader><CardTitle>Monthly Orders</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Bar :data="monthlyOrdersData" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>
            <Card class="border-border bg-card/95">
                <CardHeader><CardTitle>Best-selling Products</CardTitle></CardHeader>
                <CardContent class="space-y-2 text-sm">
                    <div v-for="product in props.bestSellingProducts" :key="product.name" class="flex justify-between border-b pb-1 last:border-none">
                        <span>{{ product.name }}</span>
                        <span>{{ product.total_quantity }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Low Stock Products</CardTitle></CardHeader>
                <CardContent class="space-y-2 text-sm">
                    <div v-for="product in props.lowStockProducts" :key="product.id" class="flex justify-between border-b pb-1 last:border-none">
                        <span>{{ product.name }}</span>
                        <span>{{ product.stock }}</span>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Recent Orders</CardTitle></CardHeader>
                <CardContent class="space-y-2 text-sm">
                    <div v-for="order in props.recentOrders" :key="order.id" class="flex justify-between border-b pb-2 last:border-none">
                        <span>#{{ order.id }} - {{ order.user.name }}</span>
                        <span>${{ order.total }} ({{ order.status.replaceAll('_', ' ') }})</span>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
