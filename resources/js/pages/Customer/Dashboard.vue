<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ProductImage from '@/components/ProductImage.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { store as addToCart } from '@/routes/customer/cart';
import { dashboard } from '@/routes';
import { index as customerCartIndex } from '@/routes/customer/cart';
import { index as customerOrdersIndex } from '@/routes/customer/orders';
import { index as customerProductsIndex } from '@/routes/customer/products';
import { show as customerProductShow } from '@/routes/customer/products';

type Order = {
    id: number;
    status: string;
    total: string;
    order_title: string;
    created_at_human: string;
};

type Product = {
    id: number;
    name: string;
    price: string;
    stock: number;
    image_url: string;
};

const props = defineProps<{
    totalOrders: number;
    pendingOrders: number;
    deliveredOrders: number;
    recentOrders: Order[];
    recommendedProducts: Product[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});
</script>

<template>
    <Head title="Customer Dashboard" />

    <div class="space-y-4 p-4">
        <h1 class="text-2xl font-semibold">Welcome to Storify</h1>

        <div class="grid gap-4 sm:grid-cols-3">
            <Card>
                <CardHeader><CardTitle class="text-sm">Total Orders</CardTitle></CardHeader>
                <CardContent class="text-2xl font-semibold">{{ props.totalOrders }}</CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle class="text-sm">Pending Orders</CardTitle></CardHeader>
                <CardContent class="text-2xl font-semibold">{{ props.pendingOrders }}</CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle class="text-sm">Delivered Orders</CardTitle></CardHeader>
                <CardContent class="text-2xl font-semibold">{{ props.deliveredOrders }}</CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader><CardTitle>Quick Actions</CardTitle></CardHeader>
            <CardContent class="flex flex-wrap gap-2">
                <Link :href="customerProductsIndex()" class="rounded-md border px-3 py-2 text-sm">Browse Products</Link>
                <Link :href="customerCartIndex()" class="rounded-md border px-3 py-2 text-sm">View Cart</Link>
                <Link :href="customerOrdersIndex()" class="rounded-md border px-3 py-2 text-sm">View Orders</Link>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Recent Orders</CardTitle></CardHeader>
            <CardContent class="space-y-2 text-sm">
                <div
                    v-for="order in props.recentOrders"
                    :key="order.id"
                    class="border-b pb-2 last:border-none"
                >
                    <p class="font-medium">{{ order.order_title }}</p>
                    <p class="text-xs text-muted-foreground">
                        Order #{{ order.id }} • <span class="capitalize">{{ order.status.replaceAll('_', ' ') }}</span> • {{ order.created_at_human }}
                    </p>
                    <p class="mt-1 font-semibold">${{ order.total }}</p>
                </div>
                <div v-if="props.recentOrders.length === 0" class="text-muted-foreground">
                    You have not placed any orders yet.
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Recommended Products</CardTitle></CardHeader>
            <CardContent>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <div
                        v-for="product in props.recommendedProducts"
                        :key="product.id"
                        class="rounded-lg border p-3"
                    >
                        <ProductImage
                            :src="product.image_url"
                            :alt="product.name"
                            class="mb-3 h-36 w-full rounded-md object-cover"
                        />
                        <p class="font-medium">{{ product.name }}</p>
                        <p class="mb-3 text-sm text-muted-foreground">${{ product.price }}</p>
                        <div class="flex gap-2">
                            <Button as-child variant="outline" size="sm">
                                <Link :href="customerProductShow(product.id)">View Details</Link>
                            </Button>
                            <Button as-child size="sm" :disabled="product.stock < 1">
                                <Link
                                    as="button"
                                    method="post"
                                    :href="addToCart()"
                                    :data="{ product_id: product.id, quantity: 1 }"
                                >
                                    Add to Cart
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>
                <p v-if="props.recommendedProducts.length === 0" class="text-sm text-muted-foreground">
                    No recommended products available yet.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
