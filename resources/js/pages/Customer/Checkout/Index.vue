<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { Component } from 'vue';
import { BookUser, CreditCard, Truck } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import ProductImage from '@/components/ProductImage.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Stepper,
    StepperDescription,
    StepperIndicator,
    StepperItem,
    StepperSeparator,
    StepperTitle,
    StepperTrigger,
} from '@/components/ui/stepper';
import { index as cartIndex } from '@/routes/customer/cart';
import { index as checkoutIndex, store } from '@/routes/customer/checkout';

type CartProduct = {
    id: number;
    name: string;
    price: string;
    image_url: string;
};

type CartItem = {
    id: number;
    quantity: number;
    product: CartProduct;
};

type ShippingMethod = {
    value: string;
    label: string;
    fee: number;
};

const props = defineProps<{
    items: CartItem[];
    subtotal: string;
    shippingMethods: ShippingMethod[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Checkout',
                href: checkoutIndex(),
            },
        ],
    },
});

const activeStep = ref(1);
const selectedShippingMethod = ref('standard');
const selectedPaymentMethod = ref('cod');

type CheckoutStep = {
    step: number;
    title: string;
    description: string;
    icon?: Component;
};

const steps: CheckoutStep[] = [
    {
        step: 1,
        title: 'Address',
        description: 'Add your address',
        icon: BookUser,
    },
    {
        step: 2,
        title: 'Shipping',
        description: 'Set your preferred',
        icon: Truck,
    },
    {
        step: 3,
        title: 'Payment',
        description: 'Add any payment',
        icon: CreditCard,
    },
    {
        step: 4,
        title: 'Checkout',
        description: 'Confirm your order',
    },
];

const shippingFee = computed(() => {
    return (
        props.shippingMethods.find((method) => method.value === selectedShippingMethod.value)?.fee ?? 0
    );
});

const totalAmount = computed(() => {
    return Number(props.subtotal) + shippingFee.value;
});
</script>

<template>
    <Head title="Checkout" />

    <div class="space-y-4 bg-background p-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h1 class="text-2xl font-semibold">Checkout</h1>
            <Button as-child variant="outline">
                <Link :href="cartIndex()">View Cart</Link>
            </Button>
        </div>

        <Stepper v-model="activeStep" class="flex w-full items-start gap-2">
            <StepperItem
                v-for="item in steps"
                :key="item.step"
                :step="item.step"
                class="relative flex w-full flex-col items-center justify-center"
            >
                <StepperTrigger>
                    <StepperIndicator class="bg-muted">
                        <template v-if="item.icon">
                            <component :is="item.icon" class="h-4 w-4" />
                        </template>
                        <span v-else>{{ item.step }}</span>
                    </StepperIndicator>
                </StepperTrigger>
                <StepperSeparator
                    v-if="item.step !== steps[steps.length - 1]?.step"
                    class="absolute top-5 right-[calc(-50%+10px)] left-[calc(50%+20px)] block h-0.5 rounded-full bg-muted group-data-[state=completed]:bg-primary"
                />
                <div class="flex flex-col items-center">
                    <StepperTitle>{{ item.title }}</StepperTitle>
                    <StepperDescription>{{ item.description }}</StepperDescription>
                </div>
            </StepperItem>
        </Stepper>

        <Form
            v-bind="store.form()"
            class="grid gap-4 lg:grid-cols-3"
            v-slot="{ errors, processing }"
        >
            <div class="space-y-4 lg:col-span-2">
                <Card v-show="activeStep === 1" class="border-border bg-card/95">
                    <CardHeader><CardTitle>Address Details</CardTitle></CardHeader>
                    <CardContent class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="full_name">Full Name</Label>
                            <Input id="full_name" name="full_name" required />
                            <InputError :message="errors.full_name" />
                        </div>
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="address">Address</Label>
                            <Input id="address" name="address" required />
                            <InputError :message="errors.address" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="contact_number">Contact Number</Label>
                            <Input id="contact_number" name="contact_number" required />
                            <InputError :message="errors.contact_number" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="city">City</Label>
                            <Input id="city" name="city" required />
                            <InputError :message="errors.city" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="postal_code">Postal Code</Label>
                            <Input id="postal_code" name="postal_code" required />
                            <InputError :message="errors.postal_code" />
                        </div>

                        <div class="md:col-span-2">
                            <Button type="button" @click="activeStep = 2">Continue to Shipping</Button>
                        </div>
                    </CardContent>
                </Card>

                <Card v-show="activeStep === 2" class="border-border bg-card/95">
                    <CardHeader><CardTitle>Shipping Method</CardTitle></CardHeader>
                    <CardContent class="grid grid-cols-1 gap-3">
                        <div class="grid gap-2 md:col-span-2">
                            <Label>Shipping Method</Label>
                            <div
                                v-for="method in props.shippingMethods"
                                :key="method.value"
                                class="flex items-center justify-between rounded-md border border-border bg-muted/20 p-3"
                            >
                                <label class="flex items-center gap-2">
                                    <input
                                        v-model="selectedShippingMethod"
                                        type="radio"
                                        name="shipping_method"
                                        :value="method.value"
                                        class="accent-primary"
                                    />
                                    {{ method.label }}
                                </label>
                                <span class="text-sm">${{ method.fee.toFixed(2) }}</span>
                            </div>
                            <InputError :message="errors.shipping_method" />
                        </div>

                        <div class="flex gap-2">
                            <Button type="button" variant="outline" @click="activeStep = 1">Back</Button>
                            <Button type="button" @click="activeStep = 3">Continue to Payment</Button>
                        </div>
                    </CardContent>
                </Card>

                <Card v-show="activeStep === 3" class="border-border bg-card/95">
                    <CardHeader><CardTitle>Payment</CardTitle></CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex items-center justify-between rounded-md border border-border bg-muted/20 p-3">
                            <label class="flex items-center gap-2">
                                <input
                                    v-model="selectedPaymentMethod"
                                    type="radio"
                                    name="payment_method"
                                    value="cod"
                                    class="accent-primary"
                                />
                                Cash on Delivery
                            </label>
                        </div>
                        <div class="flex items-center justify-between rounded-md border border-border bg-muted/20 p-3">
                            <label class="flex items-center gap-2">
                                <input
                                    v-model="selectedPaymentMethod"
                                    type="radio"
                                    name="payment_method"
                                    value="card"
                                    class="accent-primary"
                                />
                                Card Payment
                            </label>
                        </div>
                        <InputError :message="errors.payment_method" />
                        <div class="flex gap-2">
                            <Button type="button" variant="outline" @click="activeStep = 2">Back</Button>
                            <Button type="button" @click="activeStep = 4">Review Order</Button>
                        </div>
                    </CardContent>
                </Card>

                <Card v-show="activeStep === 4" class="border-border bg-card/95">
                    <CardHeader><CardTitle>Checkout</CardTitle></CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            v-for="item in props.items"
                            :key="item.id"
                            class="flex items-center justify-between border-b pb-3"
                        >
                            <div class="flex items-center gap-3">
                                <ProductImage
                                    :src="item.product.image_url"
                                    :alt="item.product.name"
                                    class="h-12 w-12 rounded-md object-cover"
                                />
                                <div>
                                    <p class="font-medium">{{ item.product.name }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        Qty: {{ item.quantity }}
                                    </p>
                                </div>
                            </div>
                            <p class="font-medium">
                                ${{ (Number(item.product.price) * item.quantity).toFixed(2) }}
                            </p>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Please review your order details and place your order.
                        </p>
                        <div class="flex gap-2">
                            <Button type="button" variant="outline" @click="activeStep = 3">Back</Button>
                            <Button type="submit" :disabled="processing">Place Order</Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card class="h-fit border-border bg-card/95">
                <CardHeader>
                    <CardTitle>Order Summary</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Subtotal</span>
                        <span>${{ Number(props.subtotal).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Shipping Fee</span>
                        <span>${{ shippingFee.toFixed(2) }}</span>
                    </div>
                    <div class="border-t pt-2 text-sm font-semibold">
                        <div class="flex justify-between">
                            <span>Total</span>
                            <span>${{ totalAmount.toFixed(2) }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </Form>
    </div>
</template>
