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
            borderColor: '#60a5fa',
            backgroundColor: 'rgba(96,165,250,0.25)',
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
                '#60a5fa',
                '#34d399',
                '#fbbf24',
                '#f97316',
                '#a78bfa',
                '#22d3ee',
                '#f87171',
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
            backgroundColor: '#818cf8',
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: '#cbd5e1',
            },
        },
    },
    scales: {
        x: {
            ticks: { color: '#94a3b8' },
            grid: { color: 'rgba(148,163,184,0.15)' },
        },
        y: {
            ticks: { color: '#94a3b8' },
            grid: { color: 'rgba(148,163,184,0.15)' },
        },
    },
} as const;
</script>

<template>
    <Head title="Analytics" />

    <div class="space-y-4 p-4">
        <h1 class="text-2xl font-semibold">Storify Analytics</h1>

        <div class="grid gap-4 sm:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Total Sales</CardTitle></CardHeader>
                <CardContent class="text-2xl font-semibold">${{ props.totalSales }}</CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Total Orders</CardTitle></CardHeader>
                <CardContent class="text-2xl font-semibold">{{ props.totalOrders }}</CardContent>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Sales Overview</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Line :data="salesOverviewData" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Orders by Status</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Doughnut :data="ordersByStatusData" :options="{ responsive: true, maintainAspectRatio: false }" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Monthly Orders</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Bar :data="monthlyOrdersData" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>
            <Card>
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
