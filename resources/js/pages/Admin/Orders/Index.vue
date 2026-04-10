<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { index, show } from '@/routes/admin/orders';

type Order = {
    id: number;
    status: string;
    total: string;
    created_at: string;
    user: { name: string };
};

type PaginatedOrders = {
    data: Order[];
    prev_page_url: string | null;
    next_page_url: string | null;
    current_page: number;
    last_page: number;
};

const props = defineProps<{
    filters: { status?: string; date?: string; customer?: string };
    statuses: string[];
    orders: PaginatedOrders;
}>();
</script>

<template>
    <Head title="Admin Orders" />

    <div class="space-y-4 p-4">
        <h1 class="text-2xl font-semibold">Orders</h1>

        <Card>
            <CardHeader><CardTitle>Filters</CardTitle></CardHeader>
            <CardContent>
                <Form v-bind="index.form()" class="grid gap-3 md:grid-cols-3">
                    <Input name="customer" :default-value="props.filters.customer" placeholder="Filter by customer" />
                    <Input name="date" type="date" :default-value="props.filters.date" />
                    <select
                        name="status"
                        :value="props.filters.status"
                        class="h-9 rounded-md border px-3 text-sm"
                    >
                        <option value="">All statuses</option>
                        <option v-for="status in props.statuses" :key="status" :value="status">
                            {{ status.replaceAll('_', ' ') }}
                        </option>
                    </select>
                    <Button type="submit" class="md:col-span-3 w-full md:w-fit">Apply Filters</Button>
                </Form>
            </CardContent>
        </Card>

        <Card>
            <CardContent class="pt-6">
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full min-w-[680px] text-sm">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">Order</th>
                                <th class="px-4 py-3 text-left font-medium">Customer</th>
                                <th class="px-4 py-3 text-left font-medium">Date</th>
                                <th class="px-4 py-3 text-left font-medium">Status</th>
                                <th class="px-4 py-3 text-left font-medium">Total</th>
                                <th class="px-4 py-3 text-left font-medium">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in props.orders.data" :key="order.id" class="border-t">
                                <td class="px-4 py-3">#{{ order.id }}</td>
                                <td class="px-4 py-3">{{ order.user.name }}</td>
                                <td class="px-4 py-3">{{ order.created_at }}</td>
                                <td class="px-4 py-3">
                                    <Badge variant="outline">{{ order.status.replaceAll('_', ' ') }}</Badge>
                                </td>
                                <td class="px-4 py-3">${{ order.total }}</td>
                                <td class="px-4 py-3">
                                    <Button as-child size="sm" variant="outline">
                                        <Link :href="show(order.id)">Details</Link>
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
