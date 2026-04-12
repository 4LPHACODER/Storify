<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Star } from 'lucide-vue-next';
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
const selectedRating = ref<number>(5);
const feedback = ref('');
const formError = ref<string | null>(null);

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

    selectedRating.value = 5;
    feedback.value = '';
    formError.value = null;
    orderToConfirm.value = order;
};

const confirmOrderReceived = () => {
    if (!orderToConfirm.value) {
        return;
    }

    if (selectedRating.value < 1 || selectedRating.value > 5) {
        formError.value = 'Please select a rating between 1 and 5 stars.';

        return;
    }

    const orderId = orderToConfirm.value.id;
    formError.value = null;

    router.patch(
        updateStatus(orderId).url,
        {
            status: 'received',
            rating: selectedRating.value,
            feedback: feedback.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                orderToConfirm.value = null;
                successMessage.value = 'Order marked as received successfully.';
            },
            onError: (errors) => {
                formError.value = errors.rating ?? errors.feedback ?? errors.status ?? 'Unable to submit your review right now.';
            },
        },
    );
};

const handleDialogOpenChange = (open: boolean) => {
    if (!open) {
        orderToConfirm.value = null;
        formError.value = null;
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
                        Please confirm that you have received your order and share your rating.
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4">
                    <div>
                        <p class="mb-2 text-sm font-medium">Rating</p>
                        <div class="flex items-center gap-1">
                            <button
                                v-for="star in 5"
                                :key="star"
                                type="button"
                                class="rounded-md p-1 transition hover:bg-muted"
                                @click="selectedRating = star"
                            >
                                <Star
                                    class="h-5 w-5"
                                    :class="star <= selectedRating ? 'fill-primary text-primary' : 'text-muted-foreground'"
                                />
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="order-feedback" class="mb-2 block text-sm font-medium">Feedback (optional)</label>
                        <textarea
                            id="order-feedback"
                            v-model="feedback"
                            rows="3"
                            maxlength="1000"
                            placeholder="Share your experience with this order..."
                            class="w-full rounded-md border border-input bg-input px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground"
                        />
                    </div>
                    <p v-if="formError" class="text-xs text-destructive">{{ formError }}</p>
                </div>
                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="outline">Cancel</Button>
                    </DialogClose>
                    <Button type="button" @click="confirmOrderReceived">Submit</Button>
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
                                Receive Order
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
