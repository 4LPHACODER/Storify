<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ProductImage from '@/components/ProductImage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { buyNow, index as cartIndex, store as addToCart } from '@/routes/customer/cart';
import { index } from '@/routes/customer/products';

type Product = {
    id: number;
    name: string;
    description: string;
    price: string;
    stock: number;
    image_url: string;
};

const props = defineProps<{
    product: Product;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Products',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head :title="props.product.name" />

    <div class="p-4">
        <Card class="overflow-hidden">
            <ProductImage
                :src="props.product.image_url"
                :alt="props.product.name"
                class="h-60 w-full object-cover sm:h-80"
            />

            <CardContent class="space-y-4 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h1 class="text-2xl font-semibold">{{ props.product.name }}</h1>
                    <Badge
                        :variant="
                            props.product.stock > 0 ? 'default' : 'destructive'
                        "
                    >
                        {{
                            props.product.stock > 0 ? 'Available' : 'Out of stock'
                        }}
                    </Badge>
                </div>
                <p class="text-sm leading-6 text-muted-foreground">
                    {{ props.product.description }}
                </p>
                <div class="text-lg font-semibold">${{ props.product.price }}</div>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <Button as-child variant="outline" :disabled="props.product.stock < 1">
                        <Link
                            as="button"
                            method="post"
                            :href="addToCart()"
                            :data="{ product_id: props.product.id, quantity: 1 }"
                        >
                            Add to Cart
                        </Link>
                    </Button>
                    <Button as-child :disabled="props.product.stock < 1">
                        <Link
                            as="button"
                            method="post"
                            :href="buyNow()"
                            :data="{ product_id: props.product.id }"
                        >
                            Buy Now
                        </Link>
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="cartIndex()">View Cart</Link>
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="index()">Back to Products</Link>
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
