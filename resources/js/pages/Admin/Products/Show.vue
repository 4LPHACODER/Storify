<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ProductImage from '@/components/ProductImage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { edit, index } from '@/routes/admin/products';

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
                title: 'Admin Products',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head :title="props.product.name" />

    <div class="bg-background p-4">
        <Card class="border-border bg-card/95">
            <CardHeader class="flex flex-row items-start justify-between">
                <CardTitle>{{ props.product.name }}</CardTitle>
                <Badge
                    :variant="
                        props.product.stock > 0 ? 'default' : 'destructive'
                    "
                >
                    {{
                        props.product.stock > 0
                            ? 'In stock'
                            : 'Out of stock'
                    }}
                </Badge>
            </CardHeader>
            <CardContent class="space-y-4">
                <ProductImage
                    :src="props.product.image_url"
                    :alt="props.product.name"
                    class="h-64 w-full rounded-md object-cover md:w-96"
                />
                <p class="text-sm text-muted-foreground">
                    {{ props.product.description }}
                </p>
                <div class="text-sm">
                    <p><strong>Price:</strong> ${{ props.product.price }}</p>
                    <p><strong>Stock:</strong> {{ props.product.stock }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <Button as-child>
                        <Link :href="edit(props.product.id)">Edit Product</Link>
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="index()">Back</Link>
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
