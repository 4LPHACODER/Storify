<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ProductImage from '@/components/ProductImage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle } from '@/components/ui/sheet';
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
const page = usePage<{ auth?: { user?: { role?: string } } }>();

const selectedProduct = ref<ProductItem | null>(null);
const isProductSheetOpen = ref(false);
const selectedQuantity = ref(1);

const isSelectedOutOfStock = computed(
    () => !selectedProduct.value || selectedProduct.value.stock < 1,
);
const isCustomerUser = computed(() => page.props.auth?.user?.role === 'customer');

const openProductSheet = (product: ProductItem) => {
    if (!isCustomerUser.value) {
        return;
    }

    selectedProduct.value = product;
    selectedQuantity.value = 1;
    isProductSheetOpen.value = true;
};

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

    <div class="space-y-4 bg-background p-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h1 class="text-2xl font-semibold">Products</h1>
                <p class="text-sm text-muted-foreground">
                    Browse available products and view details.
                </p>
            </div>
            <Button v-if="isCustomerUser" as-child variant="outline">
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

        <Sheet v-if="isCustomerUser" :open="isProductSheetOpen" @update:open="isProductSheetOpen = $event">
            <SheetContent side="bottom" class="max-h-[90vh] overflow-y-auto rounded-t-xl border-border bg-card p-0">
                <template v-if="selectedProduct">
                    <SheetHeader class="px-4 pt-4 pb-2">
                        <SheetTitle>{{ selectedProduct.name }}</SheetTitle>
                        <SheetDescription>Product quick view and checkout actions</SheetDescription>
                    </SheetHeader>

                    <div class="space-y-4 px-4 pb-4">
                        <ProductImage
                            :src="selectedProduct.image_url"
                            :alt="selectedProduct.name"
                            class="h-56 w-full rounded-lg object-cover"
                        />
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-bold text-primary">${{ selectedProduct.price }}</p>
                                <Badge :variant="selectedProduct.stock > 0 ? 'default' : 'destructive'">
                                    {{ selectedProduct.stock > 0 ? `${selectedProduct.stock} in stock` : 'Out of stock' }}
                                </Badge>
                            </div>
                            <p class="text-sm text-muted-foreground">{{ selectedProduct.description }}</p>
                        </div>

                        <div class="space-y-2">
                            <label for="sheet-quantity" class="text-sm font-medium">Quantity</label>
                            <Input
                                id="sheet-quantity"
                                v-model.number="selectedQuantity"
                                type="number"
                                min="1"
                                :max="selectedProduct.stock"
                                class="h-10"
                            />
                        </div>
                    </div>

                    <div class="sticky bottom-0 grid grid-cols-3 gap-2 border-t border-border bg-card/95 p-4 backdrop-blur">
                        <Button as-child variant="outline" :disabled="isSelectedOutOfStock">
                            <Link
                                as="button"
                                method="post"
                                :href="addToCart()"
                                :data="{ product_id: selectedProduct.id, quantity: selectedQuantity }"
                            >
                                Add to Cart
                            </Link>
                        </Button>
                        <Button as-child :disabled="isSelectedOutOfStock">
                            <Link
                                as="button"
                                method="post"
                                :href="buyNow()"
                                :data="{ product_id: selectedProduct.id, quantity: selectedQuantity }"
                            >
                                Order
                            </Link>
                        </Button>
                        <Button as-child variant="secondary" :disabled="isSelectedOutOfStock">
                            <Link
                                as="button"
                                method="post"
                                :href="buyNow()"
                                :data="{ product_id: selectedProduct.id, quantity: selectedQuantity }"
                            >
                                Buy Now
                            </Link>
                        </Button>
                    </div>
                    <div class="px-4 pb-4">
                        <Button as-child variant="ghost" class="w-full">
                            <Link :href="show(selectedProduct.id)">Open Full Product Page</Link>
                        </Button>
                    </div>
                </template>
            </SheetContent>
        </Sheet>

        <div class="grid grid-cols-2 gap-2 sm:grid-cols-2 sm:gap-3 lg:grid-cols-3">
            <Card
                v-for="product in props.products.data"
                :key="product.id"
                class="overflow-hidden rounded-lg border-border bg-card/95 shadow-sm shadow-black/25"
            >
                <button
                    v-if="isCustomerUser"
                    class="w-full text-left"
                    type="button"
                    @click="openProductSheet(product)"
                >
                    <ProductImage
                        :src="product.image_url"
                        :alt="product.name"
                        class="h-28 w-full object-cover sm:h-36"
                    />

                    <CardHeader class="space-y-1 px-3 pt-3 pb-1">
                        <CardTitle class="line-clamp-2 text-sm leading-5 sm:text-base">{{ product.name }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2 px-3 pb-3">
                        <p class="line-clamp-1 text-xs text-muted-foreground sm:text-sm">
                            {{ product.description }}
                        </p>
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold text-primary sm:text-base">${{ product.price }}</p>
                            <Badge
                                :variant="product.stock > 0 ? 'default' : 'destructive'"
                                class="text-[10px] sm:text-xs"
                            >
                                {{ product.stock > 0 ? 'In stock' : 'Out' }}
                            </Badge>
                        </div>
                    </CardContent>
                </button>
                <Link v-else :href="show(product.id)" class="block w-full text-left">
                    <ProductImage
                        :src="product.image_url"
                        :alt="product.name"
                        class="h-28 w-full object-cover sm:h-36"
                    />

                    <CardHeader class="space-y-1 px-3 pt-3 pb-1">
                        <CardTitle class="line-clamp-2 text-sm leading-5 sm:text-base">{{ product.name }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2 px-3 pb-3">
                        <p class="line-clamp-1 text-xs text-muted-foreground sm:text-sm">
                            {{ product.description }}
                        </p>
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold text-primary sm:text-base">${{ product.price }}</p>
                            <Badge
                                :variant="product.stock > 0 ? 'default' : 'destructive'"
                                class="text-[10px] sm:text-xs"
                            >
                                {{ product.stock > 0 ? 'In stock' : 'Out' }}
                            </Badge>
                        </div>
                    </CardContent>
                </Link>

                <div v-if="isCustomerUser" class="grid grid-cols-2 gap-1 border-t border-border p-2">
                    <Button as-child variant="outline" size="sm" :disabled="product.stock < 1" class="h-8 text-xs">
                        <Link
                            as="button"
                            method="post"
                            :href="addToCart()"
                            :data="{ product_id: product.id, quantity: 1 }"
                        >
                            Add
                        </Link>
                    </Button>
                    <Button as-child size="sm" :disabled="product.stock < 1" class="h-8 text-xs">
                        <Link
                            as="button"
                            method="post"
                            :href="buyNow()"
                            :data="{ product_id: product.id, quantity: 1 }"
                        >
                            Order
                        </Link>
                    </Button>
                    <Button as-child variant="ghost" size="sm" class="col-span-2 h-8 text-xs">
                        <Link :href="show(product.id)">View Details</Link>
                    </Button>
                </div>
                <div v-else class="border-t border-border p-2">
                    <Button as-child variant="ghost" size="sm" class="w-full">
                        <Link :href="show(product.id)">View Details</Link>
                    </Button>
                </div>
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
