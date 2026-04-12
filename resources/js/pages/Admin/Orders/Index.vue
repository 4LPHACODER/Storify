<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ProductImage from '@/components/ProductImage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { index, show } from '@/routes/admin/orders';

type OrderItem = {
    id: number;
    name: string;
    quantity: number;
    product?: { image_url: string };
};

type Order = {
    id: number;
    status: string;
    total: string;
    created_at: string;
    payment_method: string;
    shipping_method: string;
    delivery_estimate_label?: string | null;
    estimated_delivery_date?: string | null;
    contact_number: string;
    address: string;
    city: string;
    user: { name: string; email: string };
    items: OrderItem[];
};

type PaginatedOrders = {
    data: Order[];
    prev_page_url: string | null;
    next_page_url: string | null;
    current_page: number;
    last_page: number;
};

const props = defineProps<{
    filters: { status?: string; date?: string; customer?: string };
    statuses: string[];
    orders: PaginatedOrders;
}>();
</script>

<template>
    <Head title="Admin Orders" />

    <div class="space-y-4 bg-background p-4">
        <h1 class="text-2xl font-semibold">Orders</h1>

        <Card class="border-border bg-card/95">
            <CardHeader><CardTitle>Filters</CardTitle></CardHeader>
            <CardContent>
                <Form v-bind="index.form()" class="grid gap-3 md:grid-cols-3">
                    <Input name="customer" :default-value="props.filters.customer" placeholder="Filter by customer" />
                    <Input name="date" type="date" :default-value="props.filters.date" />
                    <select
                        name="status"
                        :value="props.filters.status"
                        class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground outline-none ring-offset-background focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        <option value="" class="bg-background text-foreground">All statuses</option>
                        <option v-for="status in props.statuses" :key="status" :value="status" class="bg-background text-foreground">
                            {{ status.replaceAll('_', ' ') }}
                        </option>
                    </select>
                    <Button type="submit" class="md:col-span-3 w-full md:w-fit">Apply Filters</Button>
                </Form>
            </CardContent>
        </Card>

        <Card class="border-border bg-card/95">
            <CardContent class="pt-6">
                <div class="space-y-3">
                    <div
                        v-for="order in props.orders.data"
                        :key="order.id"
                        class="rounded-lg border border-border bg-muted/20 p-4"
                    >
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold">Order #{{ order.id }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ order.created_at }} • {{ order.user.name }} • {{ order.user.email }}
                                </p>
                            </div>
                            <Badge variant="outline">{{ order.status.replaceAll('_', ' ') }}</Badge>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground">Items</p>
                                <div
                                    v-for="item in order.items"
                                    :key="item.id"
                                    class="flex items-center gap-2"
                                >
                                    <ProductImage
                                        :src="item.product?.image_url ?? '/images/product-placeholder.svg'"
                                        :alt="item.name"
                                        class="h-10 w-10 rounded-md object-cover"
                                    />
                                    <span class="text-sm">{{ item.name }} x {{ item.quantity }}</span>
                                </div>
                                <p class="text-xs text-muted-foreground">Total items: {{ order.items.reduce((sum, item) => sum + item.quantity, 0) }}</p>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p><strong>Total:</strong> ${{ order.total }}</p>
                                <p><strong>Payment:</strong> {{ order.payment_method }}</p>
                                <p><strong>Shipping:</strong> {{ order.shipping_method }}</p>
                                <p>
                                    <strong>Delivery Estimate:</strong>
                                    {{ order.estimated_delivery_date ?? order.delivery_estimate_label ?? 'Not set' }}
                                </p>
                                <p><strong>Contact:</strong> {{ order.contact_number }}</p>
                                <p><strong>Address:</strong> {{ order.address }}, {{ order.city }}</p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <Button as-child size="sm" variant="outline">
                                <Link :href="show(order.id)">View Details</Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
