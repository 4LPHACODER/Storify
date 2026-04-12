<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { ClipboardList, Package, Search, Truck } from 'lucide-vue-next';
import { computed } from 'vue';
import ProductImage from '@/components/ProductImage.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { store as addToCart } from '@/routes/customer/cart';
import { dashboard } from '@/routes';
import { index as customerProductsIndex } from '@/routes/customer/products';
import { show as customerProductShow } from '@/routes/customer/products';

type Product = {
    id: number;
    name: string;
    price: string;
    stock: number;
    image_url: string;
};

const props = defineProps<{
    totalOrders: number;
    pendingOrders: number;
    deliveredOrders: number;
    recommendedProducts: Product[];
}>();

const page = usePage<{ auth?: { user?: { name?: string; avatar?: string | null } } }>();
const user = computed(() => page.props.auth?.user);

const summaryCards = [
    { label: 'Total Orders', value: props.totalOrders, icon: ClipboardList },
    { label: 'Pending Orders', value: props.pendingOrders, icon: Package },
    { label: 'Delivered Orders', value: props.deliveredOrders, icon: Truck },
] as const;

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});
</script>

<template>
    <Head title="Customer Dashboard" />

    <div class="space-y-3 bg-background p-3 sm:space-y-4 sm:p-4">
        <div class="rounded-xl border border-border bg-gradient-to-br from-card to-muted/35 p-4 shadow-lg shadow-black/15">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="h-14 w-14 overflow-hidden rounded-full border border-primary/45 bg-muted shadow-[0_0_18px_rgba(29,185,84,0.25)] sm:h-16 sm:w-16">
                        <img
                            v-if="user?.avatar"
                            :src="String(user.avatar)"
                            :alt="`${user?.name ?? 'User'} avatar`"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center text-sm font-semibold text-muted-foreground"
                        >
                            {{ String(user?.name ?? 'U').charAt(0).toUpperCase() }}
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold leading-tight sm:text-[1.75rem]">{{ user?.name ?? 'Customer' }}</h1>
                        <p class="mt-1 text-sm text-muted-foreground">Storify User</p>
                    </div>
                </div>
            </div>
        </div>

        <Form v-bind="customerProductsIndex.form()" class="flex items-center gap-2">
            <Input
                name="search"
                placeholder="Search products..."
                class="h-10 bg-input text-foreground placeholder:text-muted-foreground"
            />
            <Button type="submit" size="icon" aria-label="Search products" class="h-10 w-10 text-white">
                <Search class="h-4 w-4" />
            </Button>
        </Form>

        <div class="flex gap-2 overflow-x-auto pb-1 sm:grid sm:grid-cols-3 sm:overflow-visible sm:pb-0">
            <Card
                v-for="card in summaryCards"
                :key="card.label"
                class="min-w-[148px] shrink-0 border-border bg-card/95 shadow-sm shadow-black/20 sm:min-w-0"
            >
                <CardHeader class="flex flex-row items-center justify-between p-3 pb-1">
                    <CardTitle class="text-xs sm:text-sm">{{ card.label }}</CardTitle>
                    <card.icon class="h-4 w-4 text-primary" />
                </CardHeader>
                <CardContent class="p-3 pt-0 text-xl font-semibold sm:text-2xl">{{ card.value }}</CardContent>
            </Card>
        </div>

        <Card class="border-border bg-card/95 shadow-sm shadow-black/20">
            <CardHeader class="pb-2"><CardTitle>Recommended Products</CardTitle></CardHeader>
            <CardContent>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="product in props.recommendedProducts"
                        :key="product.id"
                        class="rounded-lg border border-border bg-muted/20 p-2 shadow-sm shadow-black/10 sm:p-3"
                    >
                        <ProductImage
                            :src="product.image_url"
                            :alt="product.name"
                            class="mb-2 h-24 w-full rounded-md object-cover sm:mb-3 sm:h-36"
                        />
                        <p class="line-clamp-2 text-sm font-medium">{{ product.name }}</p>
                        <p class="mb-2 text-sm text-muted-foreground sm:mb-3">${{ product.price }}</p>
                        <div class="grid grid-cols-2 gap-1.5 sm:flex sm:gap-2">
                            <Button as-child variant="outline" size="sm" class="h-8 text-[11px] sm:h-9 sm:text-xs">
                                <Link :href="customerProductShow(product.id)">View Details</Link>
                            </Button>
                            <Button as-child size="sm" :disabled="product.stock < 1" class="h-8 text-[11px] sm:h-9 sm:text-xs">
                                <Link
                                    as="button"
                                    method="post"
                                    :href="addToCart()"
                                    :data="{ product_id: product.id, quantity: 1 }"
                                >
                                    Add to Cart
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>
                <p v-if="props.recommendedProducts.length === 0" class="text-sm text-muted-foreground">
                    No recommended products available yet.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
