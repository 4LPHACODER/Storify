<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BarChart3, Boxes, ShoppingBag, Truck } from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const features = [
    {
        title: 'Product Management',
        description:
            'Organize your catalog, manage stock, and keep product data synced across admin and customer views.',
        icon: Boxes,
    },
    {
        title: 'Order Tracking',
        description:
            'Track every order lifecycle from pending to delivered with clear status visibility and operational control.',
        icon: Truck,
    },
    {
        title: 'Analytics Insights',
        description:
            'Monitor revenue trends, order performance, and low-stock alerts with actionable ecommerce metrics.',
        icon: BarChart3,
    },
    {
        title: 'Customer Shopping',
        description:
            'Deliver a smooth shopping journey with product browsing, cart, checkout, and order history.',
        icon: ShoppingBag,
    },
] as const;
</script>

<template>
    <Head title="Storify" />

    <div class="min-h-screen bg-background text-foreground">
        <header class="border-b border-border/70">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-primary-foreground"
                    >
                        <AppLogoIcon class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-lg font-semibold">Storify</p>
                        <p class="text-xs text-muted-foreground">Smart Ecommerce Management Made Simple</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-md border px-4 py-2 text-sm hover:bg-muted"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="login()" class="rounded-md border px-4 py-2 text-sm hover:bg-muted">
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
                        >
                            Get Started
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl space-y-16 px-6 py-12">
            <section class="grid items-center gap-8 lg:grid-cols-2">
                <div class="space-y-4">
                    <p class="text-sm font-medium text-primary">Modern Ecommerce Platform</p>
                    <h1 class="text-4xl font-bold tracking-tight lg:text-5xl">
                        Storify
                    </h1>
                    <p class="text-lg text-muted-foreground">
                        Smart Ecommerce Management Made Simple
                    </p>
                    <p class="max-w-2xl text-sm text-muted-foreground">
                        Manage products, process orders, monitor analytics, and improve customer shopping experience
                        from one clean and scalable platform.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <Button as-child>
                            <Link :href="$page.props.auth.user ? dashboard() : login()">Open Dashboard</Link>
                        </Button>
                        <Button as-child variant="outline">
                            <Link :href="$page.props.auth.user ? dashboard() : register()">Start Selling</Link>
                        </Button>
                    </div>
                </div>
                <Card class="border-border/80 bg-muted/20">
                    <CardHeader>
                        <CardTitle>Built for Real Ecommerce Operations</CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-3 text-sm text-muted-foreground">
                        <p>Role-based Admin and Customer portals</p>
                        <p>Live-ready product, cart, checkout, and order modules</p>
                        <p>Actionable analytics with revenue and inventory insight</p>
                    </CardContent>
                </Card>
            </section>

            <section class="space-y-6">
                <h2 class="text-2xl font-semibold">Core Features</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="feature in features" :key="feature.title">
                        <CardHeader class="pb-2">
                            <feature.icon class="mb-2 h-5 w-5 text-primary" />
                            <CardTitle class="text-base">{{ feature.title }}</CardTitle>
                        </CardHeader>
                        <CardContent class="text-sm text-muted-foreground">
                            {{ feature.description }}
                        </CardContent>
                    </Card>
                </div>
            </section>

            <section class="space-y-6">
                <h2 class="text-2xl font-semibold">How It Works</h2>
                <div class="grid gap-4 md:grid-cols-3">
                    <Card>
                        <CardHeader><CardTitle class="text-base">1. Setup Catalog</CardTitle></CardHeader>
                        <CardContent class="text-sm text-muted-foreground">
                            Add products with pricing, stock, descriptions, and images.
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader><CardTitle class="text-base">2. Process Orders</CardTitle></CardHeader>
                        <CardContent class="text-sm text-muted-foreground">
                            Customers place orders, admins track status, and fulfillment runs smoothly.
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader><CardTitle class="text-base">3. Optimize Growth</CardTitle></CardHeader>
                        <CardContent class="text-sm text-muted-foreground">
                            Use analytics insights to improve sales, stock planning, and customer experience.
                        </CardContent>
                    </Card>
                </div>
            </section>

            <section class="grid gap-4 lg:grid-cols-2">
                <Card>
                    <CardHeader><CardTitle>Admin Overview</CardTitle></CardHeader>
                    <CardContent class="space-y-2 text-sm text-muted-foreground">
                        <p>- Manage products and inventory in a clean dashboard</p>
                        <p>- Track and update order statuses with structured flow</p>
                        <p>- Monitor revenue, orders, best sellers, and low stock alerts</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader><CardTitle>Customer Experience</CardTitle></CardHeader>
                    <CardContent class="space-y-2 text-sm text-muted-foreground">
                        <p>- Discover products with responsive shopping interface</p>
                        <p>- Checkout with clear steps and shipping/payment support</p>
                        <p>- View order history and delivery tracking in one place</p>
                    </CardContent>
                </Card>
            </section>

            <section class="rounded-xl border bg-muted/30 p-8 text-center">
                <h2 class="text-2xl font-semibold">Launch and Scale with Storify</h2>
                <p class="mx-auto mt-2 max-w-2xl text-sm text-muted-foreground">
                    Move from basic storefront to complete ecommerce management with a modern, production-ready system.
                </p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <Button as-child>
                        <Link :href="$page.props.auth.user ? dashboard() : login()">Go to Dashboard</Link>
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="$page.props.auth.user ? dashboard() : register()">Create Account</Link>
                    </Button>
                </div>
            </section>
        </main>
    </div>
</template>
