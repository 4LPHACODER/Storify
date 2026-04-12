<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ProductImage from '@/components/ProductImage.vue';
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
import { Input } from '@/components/ui/input';
import { destroy, index as cartIndex, update } from '@/routes/customer/cart';
import { index as checkoutIndex } from '@/routes/customer/checkout';
import { index as productsIndex } from '@/routes/customer/products';

type CartProduct = {
    id: number;
    name: string;
    price: string;
    stock: number;
    image_url: string;
};

type CartItem = {
    id: number;
    quantity: number;
    product: CartProduct;
};

const props = defineProps<{
    items: CartItem[];
    subtotal: string;
}>();

const showEmptyCartDialog = ref(false);

const proceedToCheckout = () => {
    if (props.items.length === 0) {
        showEmptyCartDialog.value = true;

        return;
    }

    router.visit(checkoutIndex().url);
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Cart',
                href: cartIndex(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Your Cart" />

    <div class="space-y-4 bg-background p-4">
        <Dialog :open="showEmptyCartDialog" @update:open="showEmptyCartDialog = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Your Cart is Empty</DialogTitle>
                    <DialogDescription>
                        There is no order yet to checkout. Please add at least one product to your cart before proceeding.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <Button as-child>
                        <Link :href="productsIndex()">Browse Products</Link>
                    </Button>
                    <DialogClose as-child>
                        <Button variant="outline">Close</Button>
                    </DialogClose>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <div class="flex flex-wrap items-center justify-between gap-2">
            <h1 class="text-2xl font-semibold">Your Cart</h1>
            <Button as-child variant="outline">
                <Link :href="productsIndex()">Continue Shopping</Link>
            </Button>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="space-y-3 lg:col-span-2">
                <Card
                    v-for="item in props.items"
                    :key="item.id"
                    class="border-border bg-card/95"
                >
                    <CardContent class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center">
                        <ProductImage
                            :src="item.product.image_url"
                            :alt="item.product.name"
                            class="h-24 w-24 rounded-md object-cover"
                        />
                        <div class="flex-1">
                            <h2 class="font-semibold">{{ item.product.name }}</h2>
                            <p class="text-sm text-muted-foreground">
                                ${{ item.product.price }}
                            </p>
                        </div>
                        <Form
                            v-bind="update.form(item.id)"
                            class="flex items-center gap-2"
                            v-slot="{ processing }"
                        >
                            <Input
                                name="quantity"
                                type="number"
                                min="1"
                                :max="item.product.stock"
                                :default-value="item.quantity"
                                class="w-20"
                            />
                            <Button
                                type="submit"
                                size="sm"
                                variant="outline"
                                :disabled="processing"
                            >
                                Update Quantity
                            </Button>
                        </Form>
                        <Button as-child variant="destructive" size="sm">
                            <Link as="button" method="delete" :href="destroy(item.id)">
                                Remove Item
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
                <Card v-if="props.items.length === 0" class="border-border bg-card/95">
                    <CardContent class="p-6 text-center text-muted-foreground">
                        Your cart is empty.
                    </CardContent>
                </Card>
            </div>

            <Card class="h-fit border-border bg-card/95">
                <CardHeader>
                    <CardTitle>Order Summary</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span>Subtotal</span>
                        <span>${{ props.subtotal }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Shipping</span>
                        <span>Calculated at checkout</span>
                    </div>
                    <div class="border-t pt-3">
                        <Button class="w-full" type="button" @click="proceedToCheckout">
                            Proceed to Checkout
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
