<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import OrderTrackingStepper from '@/components/orders/OrderTrackingStepper.vue';
import ProductImage from '@/components/ProductImage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { show, update } from '@/routes/admin/orders';

type OrderItem = {
    id: number;
    name: string;
    quantity: number;
    line_total: string;
    product?: { image_url: string };
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
    payment_method: string;
    delivery_estimate_label?: string | null;
    estimated_delivery_date?: string | null;
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
                        <option v-for="status in props.statuses" :key="status" :value="status" class="bg-background text-foreground">
                            {{ status.replaceAll('_', ' ') }}
                        </option>
                    </select>
                    <div class="grid gap-1">
                        <Label for="delivery_estimate_label" class="text-xs">Estimate Label</Label>
                        <Input
                            id="delivery_estimate_label"
                            name="delivery_estimate_label"
                            :default-value="props.order.delivery_estimate_label ?? ''"
                            placeholder="e.g. 3 to 5 days"
                            class="h-9 w-44"
                        />
                    </div>
                    <div class="grid gap-1">
                        <Label for="estimated_delivery_date" class="text-xs">Estimated Date</Label>
                        <Input
                            id="estimated_delivery_date"
                            name="estimated_delivery_date"
                            type="date"
                            :default-value="props.order.estimated_delivery_date ? String(props.order.estimated_delivery_date).slice(0, 10) : ''"
                            class="h-9 w-44"
                        />
                    </div>
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
                    <p>
                        <strong>Delivery Estimate:</strong>
                        {{ props.order.estimated_delivery_date ?? props.order.delivery_estimate_label ?? 'Not set' }}
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Order Summary & Payment</CardTitle></CardHeader>
                <CardContent class="space-y-1 text-sm">
                    <p><strong>Subtotal:</strong> ${{ props.order.subtotal }}</p>
                    <p><strong>Shipping:</strong> ${{ props.order.shipping_fee }}</p>
                    <p><strong>Total:</strong> ${{ props.order.total }}</p>
                    <p><strong>Payment Method:</strong> {{ props.order.payment_method }}</p>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader><CardTitle>Items</CardTitle></CardHeader>
            <CardContent class="space-y-2 text-sm">
                <div v-for="item in props.order.items" :key="item.id" class="flex items-center justify-between border-b pb-2 last:border-none">
                    <div class="flex items-center gap-3">
                        <ProductImage
                            :src="item.product?.image_url ?? '/images/product-placeholder.svg'"
                            :alt="item.name"
                            class="h-12 w-12 rounded-md object-cover"
                        />
                        <span>{{ item.name }} x {{ item.quantity }}</span>
                    </div>
                    <span>${{ item.line_total }}</span>
                </div>
            </CardContent>
        </Card>

        <Button as-child variant="outline">
            <Link :href="show(props.order.id)">Refresh</Link>
        </Button>
    </div>
</template>
