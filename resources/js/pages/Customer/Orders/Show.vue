<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Star } from 'lucide-vue-next';
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
const selectedRating = ref<number>(5);
const feedback = ref('');
const formError = ref<string | null>(null);

const canMarkReceived = ['shipped', 'out_for_delivery', 'delivered'].includes(props.order.status);
const canCancel = ['pending', 'confirmed', 'packed'].includes(props.order.status);
const deliveryEstimate = props.order.estimated_delivery_date ?? props.order.delivery_estimate_label ?? 'Not set';
const orderDate = new Date(props.order.created_at).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
});

const markOrderAsReceived = () => {
    if (selectedRating.value < 1 || selectedRating.value > 5) {
        formError.value = 'Please select a rating between 1 and 5 stars.';

        return;
    }

    formError.value = null;

    router.patch(
        updateStatus(props.order.id).url,
        {
            status: 'received',
            rating: selectedRating.value,
            feedback: feedback.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                showConfirmDialog.value = false;
                successMessage.value = 'Order marked as received successfully.';
            },
            onError: (errors) => {
                formError.value = errors.rating ?? errors.feedback ?? errors.status ?? 'Unable to submit your review right now.';
            },
        },
    );
};

const openReceiveDialog = () => {
    selectedRating.value = 5;
    feedback.value = '';
    formError.value = null;
    showConfirmDialog.value = true;
};

const handleDialogOpenChange = (open: boolean) => {
    showConfirmDialog.value = open;

    if (!open) {
        formError.value = null;
    }
};
</script>

<template>
    <Head :title="`Order #${props.order.id}`" />

    <div class="space-y-4 bg-background p-4">
        <p v-if="successMessage" class="rounded-md border border-primary/40 bg-primary/10 px-3 py-2 text-sm text-primary">
            {{ successMessage }}
        </p>
        <Dialog :open="showConfirmDialog" @update:open="handleDialogOpenChange">
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
                    <Button type="button" @click="markOrderAsReceived">Submit</Button>
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
            <Button v-if="canMarkReceived" type="button" @click="openReceiveDialog">
                Receive Order
            </Button>
            <Button as-child variant="destructive" :disabled="!canCancel">
                <Link as="button" method="patch" :href="updateStatus(props.order.id)" :data="{ status: 'cancelled' }">
                    Cancel Order
                </Link>
            </Button>
        </div>
    </div>
</template>
