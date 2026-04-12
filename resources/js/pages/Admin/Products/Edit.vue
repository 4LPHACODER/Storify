<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import ProductImage from '@/components/ProductImage.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index, update } from '@/routes/admin/products';

type Product = {
    id: number;
    name: string;
    description: string;
    price: string;
    stock: number;
    image_url: string;
    image_link: string | null;
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
    <Head title="Edit Product" />

    <div class="bg-background p-4">
        <Card class="border-border bg-card/95">
            <CardHeader>
                <CardTitle>Edit Product</CardTitle>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="update.form(props.product.id)"
                    class="space-y-4"
                    v-slot="{ errors, processing }"
                    enctype="multipart/form-data"
                >
                    <div class="grid gap-2">
                        <Label for="name">Product Name</Label>
                        <Input
                            id="name"
                            name="name"
                            :default-value="props.product.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Description</Label>
                        <textarea
                            id="description"
                            name="description"
                            class="min-h-28 rounded-md border border-input bg-input px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground"
                            required
                            :default-value="props.product.description"
                        />
                        <InputError :message="errors.description" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="price">Price</Label>
                            <Input
                                id="price"
                                name="price"
                                type="number"
                                min="0"
                                step="0.01"
                                :default-value="props.product.price"
                                required
                            />
                            <InputError :message="errors.price" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="stock">Stock</Label>
                            <Input
                                id="stock"
                                name="stock"
                                type="number"
                                min="0"
                                step="1"
                                :default-value="props.product.stock"
                                required
                            />
                            <InputError :message="errors.stock" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="image">Replace Image</Label>
                        <Input id="image" name="image" type="file" accept="image/*" />
                        <InputError :message="errors.image" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="image_link">Image Link</Label>
                        <Input
                            id="image_link"
                            name="image_link"
                            type="url"
                            :default-value="props.product.image_link ?? ''"
                            placeholder="https://example.com/product.jpg"
                        />
                        <InputError :message="errors.image_link" />
                    </div>

                    <ProductImage
                        :src="props.product.image_url"
                        :alt="props.product.name"
                        class="h-24 w-24 rounded-md object-cover"
                    />

                    <div class="flex items-center gap-2">
                        <Button type="submit" :disabled="processing">Update</Button>
                        <Button as-child variant="outline">
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
