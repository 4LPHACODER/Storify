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
import { AlertTriangle, Boxes, CheckCircle2, DollarSign, PackageCheck, ShoppingBag } from 'lucide-vue-next';
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
    receivedOrders: number;
    totalCustomers: number;
    revenue: string;
    lowStockProducts: Product[];
    ordersByStatus: StatusPoint[];
    salesOverview: SalesPoint[];
    monthlyRevenueTrend: RevenuePoint[];
    bestSellingProducts: BestSellerPoint[];
}>();

const lowStockCount = computed(() => props.lowStockProducts.length);
const summaryCards = computed(() => [
    { title: 'Total Revenue', value: `$${props.revenue}`, icon: DollarSign, highlight: true },
    { title: 'Total Orders', value: props.totalOrders, icon: ShoppingBag },
    { title: 'Pending Orders', value: props.pendingOrders, icon: ShoppingBag },
    { title: 'Delivered Orders', value: props.deliveredOrders, icon: PackageCheck },
    { title: 'Received Orders', value: props.receivedOrders, icon: CheckCircle2, highlight: true },
    { title: 'Total Products', value: props.totalProducts, icon: Boxes },
    { title: 'Low Stock Products', value: lowStockCount.value, icon: AlertTriangle },
]);

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

const monthlyRevenueData = computed(() => ({
    labels: props.monthlyRevenueTrend.map((point) => point.month),
    datasets: [
        {
            label: 'Monthly Revenue',
            data: props.monthlyRevenueTrend.map((point) => Number(point.revenue)),
            backgroundColor: '#1DB954',
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

const bestSellingData = computed(() => ({
    labels: props.bestSellingProducts.map((point) => point.name),
    datasets: [
        {
            label: 'Units Sold',
            data: props.bestSellingProducts.map((point) => point.total_quantity),
            backgroundColor: '#1DB954',
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: { color: '#e5e7eb' },
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

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Admin Dashboard', href: dashboard() }],
    },
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="space-y-5 bg-background p-4">
        <div class="rounded-xl border border-border bg-gradient-to-br from-card to-muted/35 p-4 shadow-lg shadow-black/20">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-primary">Admin Control Center</p>
                    <h1 class="mt-1 text-2xl font-semibold">Storify Admin Dashboard</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Monitor operations, orders, and product performance in one place.</p>
                </div>
                <div class="flex gap-2">
                    <Button as-child variant="outline">
                        <Link :href="adminOrdersIndex()">View Orders</Link>
                    </Button>
                    <Button as-child>
                        <Link :href="adminProductsIndex()">Manage Products</Link>
                    </Button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <Card
                v-for="card in summaryCards"
                :key="card.title"
                class="border-border bg-card/95 shadow-sm shadow-black/20"
            >
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm text-muted-foreground">{{ card.title }}</CardTitle>
                    <card.icon class="size-4" :class="card.highlight ? 'text-primary' : 'text-muted-foreground'" />
                </CardHeader>
                <CardContent class="text-2xl font-semibold" :class="card.highlight ? 'text-primary' : 'text-foreground'">
                    {{ card.value }}
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card class="border-border bg-card/95 shadow-sm shadow-black/20">
                <CardHeader><CardTitle>Sales Overview</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Line :data="salesOverviewData" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>

            <Card class="border-border bg-card/95 shadow-sm shadow-black/20">
                <CardHeader><CardTitle>Monthly Revenue Trend</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Bar :data="monthlyRevenueData" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card class="border-border bg-card/95 shadow-sm shadow-black/20">
                <CardHeader><CardTitle>Orders by Status</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Doughnut :data="ordersByStatusData" :options="{ responsive: true, maintainAspectRatio: false }" />
                    </div>
                </CardContent>
            </Card>
            <Card class="border-border bg-card/95 shadow-sm shadow-black/20">
                <CardHeader><CardTitle>Best-selling Products</CardTitle></CardHeader>
                <CardContent>
                    <div class="h-72">
                        <Bar :data="bestSellingData" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card class="border-border bg-card/95 shadow-sm shadow-black/20">
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
            <Card class="border-border bg-card/95 shadow-sm shadow-black/20">
                <CardHeader><CardTitle>Admin Snapshot</CardTitle></CardHeader>
                <CardContent class="space-y-2 text-sm">
                    <div class="flex justify-between"><span>Total Customers</span><span>{{ props.totalCustomers }}</span></div>
                    <div class="flex justify-between"><span>Pending Orders</span><span>{{ props.pendingOrders }}</span></div>
                    <div class="flex justify-between"><span>Delivered Orders</span><span>{{ props.deliveredOrders }}</span></div>
                    <div class="flex justify-between"><span>Received Orders</span><span class="text-primary">{{ props.receivedOrders }}</span></div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
