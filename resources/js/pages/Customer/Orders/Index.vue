<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { show } from '@/routes/customer/orders';

type Order = {
    id: number;
    status: string;
    total: string;
    order_title: string;
    created_at_human: string;
};

type PaginatedOrders = {
    data: Order[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
};

const props = defineProps<{ orders: PaginatedOrders }>();
</script>

<template>
    <Head title="My Orders" />

    <div class="space-y-4 p-4">
        <h1 class="text-2xl font-semibold">My Orders</h1>

        <Card>
            <CardHeader><CardTitle>Order History</CardTitle></CardHeader>
            <CardContent class="space-y-2 text-sm">
                <div v-for="order in props.orders.data" :key="order.id" class="rounded-lg border p-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="font-semibold">{{ order.order_title }}</p>
                            <p class="text-xs text-muted-foreground">
                                Order #{{ order.id }} • {{ order.created_at_human }}
                            </p>
                        </div>
                        <Badge variant="outline">{{ order.status.replaceAll('_', ' ') }}</Badge>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="font-medium">${{ order.total }}</span>
                        <Link :href="show(order.id)" class="text-primary hover:underline">View details</Link>
                    </div>
                </div>
                <div v-if="props.orders.data.length === 0" class="text-muted-foreground">
                    No orders yet.
                </div>
            </CardContent>
        </Card>
    </div>
</template>
