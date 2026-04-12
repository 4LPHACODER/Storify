<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ProductImage from '@/components/ProductImage.vue';
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
import { index, show, updateStatus } from '@/routes/customer/orders';
import { dashboard } from '@/routes';

type Order = {
    id: number;
    status: string;
    total: string;
    order_title: string;
    created_at_human: string;
    items_count: number;
    shipping_method: string;
    delivery_estimate: string;
    image_url: string;
    can_mark_received: boolean;
    can_cancel: boolean;
};

type PaginatedOrders = {
    data: Order[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
};

const props = defineProps<{
    filters: { filter?: string };
    orders: PaginatedOrders;
}>();
const orderToConfirm = ref<Order | null>(null);
const successMessage = ref<string | null>(null);

const filterTabs = [
    { label: 'To Deliver', value: 'to_deliver' },
    { label: 'Received', value: 'received' },
    { label: 'Cancelled', value: 'cancelled' },
    { label: 'All Orders', value: 'all' },
] as const;

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Orders', href: dashboard() }],
    },
});

const openReceivedDialog = (order: Order) => {
    if (!order.can_mark_received) {
        return;
    }

    orderToConfirm.value = order;
};

const confirmOrderReceived = () => {
    if (!orderToConfirm.value) {
        return;
    }

    const orderId = orderToConfirm.value.id;
    orderToConfirm.value = null;

    router.patch(
        updateStatus(orderId).url,
        { status: 'received' },
        {
            preserveScroll: true,
            onSuccess: () => {
                successMessage.value = 'Order marked as received successfully.';
            },
        },
    );
};

const handleDialogOpenChange = (open: boolean) => {
    if (!open) {
        orderToConfirm.value = null;
    }
};
</script>

<template>
    <Head title="My Orders" />

    <div class="space-y-4 bg-background p-4">
        <h1 class="text-2xl font-semibold">My Orders</h1>
        <p v-if="successMessage" class="rounded-md border border-primary/40 bg-primary/10 px-3 py-2 text-sm text-primary">
            {{ successMessage }}
        </p>

        <Dialog :open="!!orderToConfirm" @update:open="handleDialogOpenChange">
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
                    <Button type="button" @click="confirmOrderReceived">Confirm</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Card class="border-border bg-card/95">
            <CardHeader><CardTitle>Order History</CardTitle></CardHeader>
            <CardContent class="space-y-2 text-sm">
                <div class="mb-3 flex flex-wrap gap-2">
                    <Button
                        v-for="tab in filterTabs"
                        :key="tab.value"
                        as-child
                        size="sm"
                        :variant="(props.filters.filter ?? 'all') === tab.value ? 'default' : 'outline'"
                    >
                        <Link :href="index({ query: { filter: tab.value } })">
                            {{ tab.label }}
                        </Link>
                    </Button>
                </div>

                <div v-for="order in props.orders.data" :key="order.id" class="rounded-lg border border-border bg-muted/20 p-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <ProductImage :src="order.image_url" :alt="order.order_title" class="h-14 w-14 rounded-md object-cover" />
                            <div>
                            <p class="font-semibold">{{ order.order_title }}</p>
                            <p class="text-xs text-muted-foreground">
                                Order #{{ order.id }} • {{ order.status.replaceAll('_', ' ') }} • {{ order.created_at_human }}
                            </p>
                            </div>
                        </div>
                        <Badge variant="outline">{{ order.status.replaceAll('_', ' ') }}</Badge>
                    </div>
                    <div class="mt-2 text-xs text-muted-foreground">
                        <p>{{ order.items_count }} item(s) • {{ order.shipping_method }}</p>
                        <p>Estimated arrival: {{ order.delivery_estimate }}</p>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                        <span class="font-medium">${{ order.total }}</span>
                        <div class="flex flex-wrap gap-2">
                            <Button as-child size="sm" variant="outline">
                                <Link :href="show(order.id)">View Details</Link>
                            </Button>
                            <Button
                                v-if="order.can_mark_received"
                                size="sm"
                                type="button"
                                @click="openReceivedDialog(order)"
                            >
                                    Order Received
                            </Button>
                            <Button as-child size="sm" variant="destructive" :disabled="!order.can_cancel">
                                <Link
                                    as="button"
                                    method="patch"
                                    :href="updateStatus(order.id)"
                                    :data="{ status: 'cancelled' }"
                                >
                                    Cancel Order
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>
                <div v-if="props.orders.data.length === 0" class="text-muted-foreground">
                    No orders yet.
                </div>
            </CardContent>
        </Card>
    </div>
</template>
