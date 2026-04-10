<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
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
import { AlertTriangle, Boxes, DollarSign, ShoppingBag } from 'lucide-vue-next';
import { computed } from 'vue';
import { Bar, Doughnut, Line } from 'vue-chartjs';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as adminOrdersIndex } from '@/routes/admin/orders';
import { index as adminProductsIndex } from '@/routes/admin/products';

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

type Product = {
    id: number;
    name: string;
    stock: number;
};

type SalesPoint = {
    date: string;
    revenue: string;
};

type RevenuePoint = {
    month: string;
    revenue: number;
};

type StatusPoint = {
    status: string;
    total: number;
};

type BestSellerPoint = {
    name: string;
    total_quantity: number;
};

const props = defineProps<{
    totalProducts: number;
    totalOrders: number;
    pendingOrders: number;
    deliveredOrders: number;
    totalCustomers: number;
    revenue: string;
    lowStockProducts: Product[];
    ordersByStatus: StatusPoint[];
    salesOverview: SalesPoint[];
    monthlyRevenueTrend: RevenuePoint[];
    bestSellingProducts: BestSellerPoint[];
}>();

const lowStockCount = computed(() => props.lowStockProducts.length);

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

const monthlyRevenueData = computed(() => ({
    labels: props.monthlyRevenueTrend.map((point) => point.month),
    datasets: [
        {
            label: 'Monthly Revenue',
            data: props.monthlyRevenueTrend.map((point) => Number(point.revenue)),
            backgroundColor: '#818cf8',
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

const bestSellingData = computed(() => ({
    labels: props.bestSellingProducts.map((point) => point.name),
    datasets: [
        {
            label: 'Units Sold',
            data: props.bestSellingProducts.map((point) => point.total_quantity),
            backgroundColor: '#34d399',
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: { color: '#cbd5e1' },
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

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Admin Dashboard', href: dashboard() }],
    },
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="space-y-4 p-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h1 class="text-2xl font-semibold">Storify Admin Dashboard</h1>
            <div class="flex gap-2">
                <Button as-child variant="outline">
                    <Link :href="adminOrdersIndex()">View Orders</Link>
                </Button>
                <Button as-child>
                    <Link :href="adminProductsIndex()">Manage Products</Link>
                </Button>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm">Total Revenue</CardTitle>
                    <DollarSign class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent class="text-2xl font-semibold">${{ props.revenue }}</CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm">Total Orders</CardTitle>
                    <ShoppingBag class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent class="text-2xl font-semibold">{{ props.totalOrders }}</CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm">Pending Orders</CardTitle>
                    <ShoppingBag class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent class="text-2xl font-semibold">{{ props.pendingOrders }}</CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm">Delivered Orders</CardTitle>
                    <ShoppingBag class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent class="text-2xl font-semibold">{{ props.deliveredOrders }}</CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm">Total Products</CardTitle>
                    <Boxes class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent class="text-2xl font-semibold">{{ props.totalProducts }}</CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm">Low Stock Products</CardTitle>
                    <AlertTriangle class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent class="text-2xl font-semibold">{{ lowStockCount }}</CardContent>
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
                <CardHeader><CardTitle>Monthly Revenue Trend</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Bar :data="monthlyRevenueData" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Orders by Status</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Doughnut :data="ordersByStatusData" :options="{ responsive: true, maintainAspectRatio: false }" />
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Best-selling Products</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Bar :data="bestSellingData" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Low Stock Alerts</CardTitle></CardHeader>
                <CardContent class="space-y-2 text-sm">
                    <div
                        v-for="product in props.lowStockProducts"
                        :key="product.id"
                        class="flex items-center justify-between border-b pb-2 last:border-none"
                    >
                        <span>{{ product.name }}</span>
                        <Badge variant="destructive">{{ product.stock }}</Badge>
                    </div>
                    <div v-if="props.lowStockProducts.length === 0" class="text-muted-foreground">
                        No low stock products.
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Admin Snapshot</CardTitle></CardHeader>
                <CardContent class="space-y-2 text-sm">
                    <div class="flex justify-between"><span>Total Customers</span><span>{{ props.totalCustomers }}</span></div>
                    <div class="flex justify-between"><span>Pending Orders</span><span>{{ props.pendingOrders }}</span></div>
                    <div class="flex justify-between"><span>Delivered Orders</span><span>{{ props.deliveredOrders }}</span></div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
