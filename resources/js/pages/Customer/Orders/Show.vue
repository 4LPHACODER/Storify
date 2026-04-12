<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ProductImage from '@/components/ProductImage.vue';
import OrderTrackingStepper from '@/components/orders/OrderTrackingStepper.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { index, updateStatus } from '@/routes/customer/orders';

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
    created_at: string;
    full_name: string;
    address: string;
    city: string;
    postal_code: string;
    contact_number: string;
    shipping_method: string;
    payment_method: string;
    delivery_estimate_label?: string | null;
    estimated_delivery_date?: string | null;
    items: OrderItem[];
};

const props = defineProps<{ order: Order }>();

const showConfirmDialog = ref(false);
const successMessage = ref<string | null>(null);

const canMarkReceived = ['shipped', 'delivered'].includes(props.order.status);
const canCancel = ['pending', 'confirmed', 'packed'].includes(props.order.status);
const deliveryEstimate = props.order.estimated_delivery_date ?? props.order.delivery_estimate_label ?? 'Not set';
const orderDate = new Date(props.order.created_at).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
});

const markOrderAsReceived = () => {
    showConfirmDialog.value = false;

    router.patch(
        updateStatus(props.order.id).url,
        { status: 'received' },
        {
            preserveScroll: true,
            onSuccess: () => {
                successMessage.value = 'Order marked as received successfully.';
            },
        },
    );
};
</script>

<template>
    <Head :title="`Order #${props.order.id}`" />

    <div class="space-y-4 bg-background p-4">
        <p v-if="successMessage" class="rounded-md border border-primary/40 bg-primary/10 px-3 py-2 text-sm text-primary">
            {{ successMessage }}
        </p>
        <Dialog :open="showConfirmDialog" @update:open="showConfirmDialog = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirm Order Receipt</DialogTitle>
                    <DialogDescription>
                        Are you sure you have received this order?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="outline">Cancel</Button>
                    </DialogClose>
                    <Button type="button" @click="markOrderAsReceived">Confirm</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Order #{{ props.order.id }}</h1>
            <Badge>{{ props.order.status.replaceAll('_', ' ') }}</Badge>
        </div>

        <Card class="border-border bg-card/95">
            <CardHeader><CardTitle>Order Metadata</CardTitle></CardHeader>
            <CardContent class="grid gap-2 text-sm md:grid-cols-2">
                <p><strong>Order Date:</strong> {{ orderDate }}</p>
                <p><strong>Estimated Arrival:</strong> {{ deliveryEstimate }}</p>
                <p><strong>Shipping Method:</strong> {{ props.order.shipping_method }}</p>
                <p><strong>Payment Method:</strong> {{ props.order.payment_method }}</p>
            </CardContent>
        </Card>

        <Card class="border-border bg-card/95">
            <CardHeader><CardTitle>Tracking</CardTitle></CardHeader>
            <CardContent>
                <OrderTrackingStepper :status="props.order.status" />
            </CardContent>
        </Card>

        <Card class="border-border bg-card/95">
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

        <Card class="border-border bg-card/95">
            <CardHeader><CardTitle>Shipping Address</CardTitle></CardHeader>
            <CardContent class="space-y-1 text-sm">
                <p><strong>Name:</strong> {{ props.order.full_name }}</p>
                <p><strong>Contact:</strong> {{ props.order.contact_number }}</p>
                <p><strong>Address:</strong> {{ props.order.address }}</p>
                <p><strong>City:</strong> {{ props.order.city }}</p>
                <p><strong>Postal Code:</strong> {{ props.order.postal_code }}</p>
            </CardContent>
        </Card>

        <Card class="border-border bg-card/95">
            <CardHeader><CardTitle>Payment Summary</CardTitle></CardHeader>
            <CardContent class="space-y-1 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><span>${{ props.order.subtotal }}</span></div>
                <div class="flex justify-between"><span>Shipping</span><span>${{ props.order.shipping_fee }}</span></div>
                <div class="flex justify-between font-semibold"><span>Total</span><span>${{ props.order.total }}</span></div>
            </CardContent>
        </Card>

        <div class="flex flex-wrap gap-2">
            <Button as-child variant="outline">
                <Link :href="index()">Back to Orders</Link>
            </Button>
            <Button v-if="canMarkReceived" type="button" @click="showConfirmDialog = true">
                    Order Received
            </Button>
            <Button as-child variant="destructive" :disabled="!canCancel">
                <Link as="button" method="patch" :href="updateStatus(props.order.id)" :data="{ status: 'cancelled' }">
                    Cancel Order
                </Link>
            </Button>
        </div>
    </div>
</template>
