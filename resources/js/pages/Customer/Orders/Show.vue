<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import OrderTrackingStepper from '@/components/orders/OrderTrackingStepper.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type OrderItem = {
    id: number;
    name: string;
    quantity: number;
    line_total: string;
};

type Order = {
    id: number;
    status: string;
    total: string;
    subtotal: string;
    shipping_fee: string;
    created_at: string;
    items: OrderItem[];
};

const props = defineProps<{ order: Order }>();

</script>

<template>
    <Head :title="`Order #${props.order.id}`" />

    <div class="space-y-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Order #{{ props.order.id }}</h1>
            <Badge>{{ props.order.status.replaceAll('_', ' ') }}</Badge>
        </div>

        <Card>
            <CardHeader><CardTitle>Tracking</CardTitle></CardHeader>
            <CardContent>
                <OrderTrackingStepper :status="props.order.status" />
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Items</CardTitle></CardHeader>
            <CardContent class="space-y-2 text-sm">
                <div v-for="item in props.order.items" :key="item.id" class="flex justify-between border-b pb-2 last:border-none">
                    <span>{{ item.name }} x {{ item.quantity }}</span>
                    <span>${{ item.line_total }}</span>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Payment Summary</CardTitle></CardHeader>
            <CardContent class="space-y-1 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><span>${{ props.order.subtotal }}</span></div>
                <div class="flex justify-between"><span>Shipping</span><span>${{ props.order.shipping_fee }}</span></div>
                <div class="flex justify-between font-semibold"><span>Total</span><span>${{ props.order.total }}</span></div>
            </CardContent>
        </Card>
    </div>
</template>
