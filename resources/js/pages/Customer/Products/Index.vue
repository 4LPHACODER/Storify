<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ProductImage from '@/components/ProductImage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { buyNow, index as cartIndex, store as addToCart } from '@/routes/customer/cart';
import { index, show } from '@/routes/customer/products';

type ProductItem = {
    id: number;
    name: string;
    description: string;
    price: string;
    stock: number;
    image_url: string;
};

type PaginatedProducts = {
    data: ProductItem[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
};

const props = defineProps<{
    products: PaginatedProducts;
    filters: {
        search?: string;
    };
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
    <Head title="Products" />

    <div class="space-y-4 p-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h1 class="text-2xl font-semibold">Products</h1>
                <p class="text-sm text-muted-foreground">
                    Browse available products and view details.
                </p>
            </div>
            <Button as-child variant="outline">
                <Link :href="cartIndex()">View Cart</Link>
            </Button>
        </div>

        <Form v-bind="index.form()" class="flex flex-col gap-2 sm:flex-row">
            <Input
                name="search"
                :default-value="props.filters.search"
                placeholder="Search products..."
                class="sm:max-w-sm"
            />
            <div class="flex gap-2">
                <Button type="submit" variant="outline">Search</Button>
                <Button as-child variant="ghost" type="button">
                    <Link :href="index()">Reset</Link>
                </Button>
            </div>
        </Form>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Card
                v-for="product in props.products.data"
                :key="product.id"
                class="overflow-hidden"
            >
                <ProductImage
                    :src="product.image_url"
                    :alt="product.name"
                    class="h-44 w-full object-cover"
                />

                <CardHeader class="pb-2">
                    <CardTitle class="text-lg">{{ product.name }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <p class="line-clamp-2 text-sm text-muted-foreground">
                        {{ product.description }}
                    </p>
                    <div class="flex items-center justify-between">
                        <p class="font-semibold">${{ product.price }}</p>
                        <Badge
                            :variant="
                                product.stock > 0 ? 'default' : 'destructive'
                            "
                        >
                            {{
                                product.stock > 0
                                    ? `${product.stock} in stock`
                                    : 'Out of stock'
                            }}
                        </Badge>
                    </div>
                    <Button as-child class="w-full">
                        <Link :href="show(product.id)">View Details</Link>
                    </Button>
                    <div class="grid grid-cols-2 gap-2">
                        <Button
                            as-child
                            variant="outline"
                            :disabled="product.stock < 1"
                        >
                            <Link
                                as="button"
                                method="post"
                                :href="addToCart()"
                                :data="{ product_id: product.id, quantity: 1 }"
                            >
                                Add to Cart
                            </Link>
                        </Button>
                        <Button as-child :disabled="product.stock < 1">
                            <Link
                                as="button"
                                method="post"
                                :href="buyNow()"
                                :data="{ product_id: product.id }"
                            >
                                Buy Now
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="flex items-center justify-between">
            <Button as-child variant="outline" :disabled="!props.products.prev_page_url">
                <Link v-if="props.products.prev_page_url" :href="props.products.prev_page_url">Previous</Link>
                <span v-else>Previous</span>
            </Button>
            <span class="text-sm text-muted-foreground">
                Page {{ props.products.current_page }} of {{ props.products.last_page }}
            </span>
            <Button as-child variant="outline" :disabled="!props.products.next_page_url">
                <Link v-if="props.products.next_page_url" :href="props.products.next_page_url">Next</Link>
                <span v-else>Next</span>
            </Button>
        </div>
    </div>
</template>
