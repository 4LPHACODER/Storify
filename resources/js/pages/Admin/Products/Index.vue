<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Package, Store, Tag, TrendingUp } from 'lucide-vue-next';
import ProductImage from '@/components/ProductImage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { index as customerProductsIndex } from '@/routes/customer/products';
import { create, destroy, edit, index, show } from '@/routes/admin/products';

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

const summaryCards = [
    {
        title: 'Total Products',
        value: props.products.data.length,
        description: 'Current page items',
        icon: Package,
    },
    {
        title: 'In Stock',
        value: props.products.data.filter((product) => product.stock > 0).length,
        description: 'Available products',
        icon: Store,
    },
    {
        title: 'Low Stock',
        value: props.products.data.filter((product) => product.stock > 0 && product.stock < 10).length,
        description: 'Need restock soon',
        icon: TrendingUp,
    },
    {
        title: 'Out of Stock',
        value: props.products.data.filter((product) => product.stock === 0).length,
        description: 'Unavailable items',
        icon: Tag,
    },
] as const;

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Admin Products',
                href: index(),
            },
        ],
    },
});

</script>

<template>
    <Head title="Admin Products" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold">Products Management</h1>
                <p class="text-sm text-muted-foreground">
                    Create, update, and remove products from your catalog.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button as-child variant="outline">
                    <Link :href="customerProductsIndex()">
                        View Customer Page
                    </Link>
                </Button>
                <Button as-child>
                    <Link :href="create()">Add Product</Link>
                </Button>
            </div>
        </div>

        <Card>
            <CardContent class="pt-6">
                <Form v-bind="index.form()" class="flex flex-col gap-2 sm:flex-row">
                    <Input
                        name="search"
                        :default-value="props.filters.search"
                        placeholder="Search products by name or description..."
                        class="sm:max-w-sm"
                    />
                    <div class="flex gap-2">
                        <Button type="submit" variant="outline">Search</Button>
                        <Button as-child variant="ghost" type="button">
                            <Link :href="index()">Reset</Link>
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>

        <Card>
            <div class="grid gap-4 p-4 sm:grid-cols-2 xl:grid-cols-4">
                <Card v-for="card in summaryCards" :key="card.title">
                    <CardHeader class="flex flex-row items-start justify-between space-y-0">
                        <div>
                            <p class="text-sm text-muted-foreground">{{ card.title }}</p>
                            <CardTitle class="text-2xl">{{ card.value }}</CardTitle>
                        </div>
                        <card.icon class="size-5 text-muted-foreground" />
                    </CardHeader>
                    <CardContent class="pt-0">
                        <p class="text-xs text-muted-foreground">{{ card.description }}</p>
                    </CardContent>
                </Card>
            </div>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>All Products</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="bg-muted/40 text-left text-muted-foreground">
                            <tr>
                                <th class="px-4 py-3 font-medium">Image</th>
                                <th class="px-4 py-3 font-medium">Name</th>
                                <th class="px-4 py-3 font-medium">Price</th>
                                <th class="px-4 py-3 font-medium">Stock</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="product in props.products.data"
                                :key="product.id"
                                class="border-t"
                            >
                                <td class="px-4 py-3">
                                    <ProductImage
                                        :src="product.image_url"
                                        :alt="product.name"
                                        class="h-12 w-12 rounded-md object-cover"
                                    />
                                </td>
                                <td class="px-4 py-3 font-medium">
                                    {{ product.name }}
                                </td>
                                <td class="px-4 py-3">${{ product.price }}</td>
                                <td class="px-4 py-3">{{ product.stock }}</td>
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="
                                            product.stock > 0
                                                ? 'default'
                                                : 'destructive'
                                        "
                                    >
                                        {{
                                            product.stock > 0
                                                ? 'Available'
                                                : 'Out of stock'
                                        }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <Button as-child size="sm" variant="outline">
                                            <Link :href="show(product.id)"
                                                >View</Link
                                            >
                                        </Button>
                                        <Button as-child size="sm" variant="outline">
                                            <Link :href="edit(product.id)"
                                                >Edit</Link
                                            >
                                        </Button>
                                        <Button
                                            as-child
                                            size="sm"
                                            variant="destructive"
                                        >
                                            <Link
                                                as="button"
                                                method="delete"
                                                :href="destroy(product.id)"
                                            >
                                                Delete
                                            </Link>
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="props.products.data.length === 0">
                                <td
                                    colspan="6"
                                    class="px-4 py-6 text-center text-muted-foreground"
                                >
                                    No products found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <Button
                        as-child
                        variant="outline"
                        :disabled="!props.products.prev_page_url"
                    >
                        <Link
                            v-if="props.products.prev_page_url"
                            :href="props.products.prev_page_url"
                            >Previous</Link
                        >
                        <span v-else>Previous</span>
                    </Button>

                    <span class="text-sm text-muted-foreground">
                        Page {{ props.products.current_page }} of
                        {{ props.products.last_page }}
                    </span>

                    <Button
                        as-child
                        variant="outline"
                        :disabled="!props.products.next_page_url"
                    >
                        <Link
                            v-if="props.products.next_page_url"
                            :href="props.products.next_page_url"
                            >Next</Link
                        >
                        <span v-else>Next</span>
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
