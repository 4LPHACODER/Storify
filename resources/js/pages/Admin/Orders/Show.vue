<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import OrderTrackingStepper from '@/components/orders/OrderTrackingStepper.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { show, update } from '@/routes/admin/orders';

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
    user: { name: string; email: string };
    full_name: string;
    address: string;
    contact_number: string;
    city: string;
    postal_code: string;
    shipping_method: string;
    items: OrderItem[];
};

const props = defineProps<{
    order: Order;
    statuses: string[];
}>();
</script>

<template>
    <Head :title="`Order #${props.order.id}`" />

    <div class="space-y-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Order #{{ props.order.id }}</h1>
            <Badge>{{ props.order.status.replaceAll('_', ' ') }}</Badge>
        </div>

        <Card>
            <CardHeader><CardTitle>Update Status</CardTitle></CardHeader>
            <CardContent>
                <Form v-bind="update.form(props.order.id)" class="flex flex-wrap items-center gap-2">
                    <select
                        name="status"
                        :value="props.order.status"
                        class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground outline-none ring-offset-background focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        <option v-for="status in props.statuses" :key="status" :value="status">
                            {{ status.replaceAll('_', ' ') }}
                        </option>
                    </select>
                    <Button type="submit">Update</Button>
                </Form>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Tracking Timeline</CardTitle></CardHeader>
            <CardContent>
                <OrderTrackingStepper :status="props.order.status" />
            </CardContent>
        </Card>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Shipping Details</CardTitle></CardHeader>
                <CardContent class="space-y-1 text-sm">
                    <p><strong>Name:</strong> {{ props.order.full_name }}</p>
                    <p><strong>Address:</strong> {{ props.order.address }}</p>
                    <p><strong>Contact:</strong> {{ props.order.contact_number }}</p>
                    <p><strong>City:</strong> {{ props.order.city }}</p>
                    <p><strong>Postal Code:</strong> {{ props.order.postal_code }}</p>
                    <p><strong>Method:</strong> {{ props.order.shipping_method }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Order Summary</CardTitle></CardHeader>
                <CardContent class="space-y-1 text-sm">
                    <p><strong>Subtotal:</strong> ${{ props.order.subtotal }}</p>
                    <p><strong>Shipping:</strong> ${{ props.order.shipping_fee }}</p>
                    <p><strong>Total:</strong> ${{ props.order.total }}</p>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader><CardTitle>Items</CardTitle></CardHeader>
            <CardContent class="space-y-2 text-sm">
                <div v-for="item in props.order.items" :key="item.id" class="flex justify-between border-b pb-2 last:border-none">
                    <span>{{ item.name }} x {{ item.quantity }}</span>
                    <span>${{ item.line_total }}</span>
                </div>
            </CardContent>
        </Card>

        <Button as-child variant="outline">
            <Link :href="show(props.order.id)">Refresh</Link>
        </Button>
    </div>
</template>
