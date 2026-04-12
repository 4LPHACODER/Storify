<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { BarChart3, CheckCircle2, ChevronLeft, ChevronRight, ClipboardList, PackageSearch, ShoppingBag, Sparkles, Store } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
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
        title: 'Smart Product Browsing',
        description:
            'Discover products easily with a clean layout, organized listings, and a shopping experience designed for fast decision-making.',
        icon: Store,
    },
    {
        title: 'Easy Order Tracking',
        description:
            'Follow your orders with clear updates, delivery progress, and a simpler way to stay informed from purchase to arrival.',
        icon: ClipboardList,
    },
    {
        title: 'Purchase Insights',
        description:
            'View useful shopping and order information through organized summaries, status updates, and a clearer buying journey.',
        icon: BarChart3,
    },
    {
        title: 'Smooth Checkout Experience',
        description:
            'Enjoy a fast and user-friendly checkout flow with simple actions for cart review, order placement, and confirmation.',
        icon: ShoppingBag,
    },
] as const;

const showcaseSlides = [
    { src: '/Storify_1.png', alt: 'Storify platform preview 1' },
    { src: '/Storify_2.png', alt: 'Storify platform preview 2' },
    { src: '/Storify_3.png', alt: 'Storify platform preview 3' },
] as const;

const activeSlide = ref(0);
const touchStartX = ref<number | null>(null);
let slideInterval: ReturnType<typeof setInterval> | null = null;

const goToSlide = (index: number) => {
    activeSlide.value = index;
};

const nextSlide = () => {
    activeSlide.value = (activeSlide.value + 1) % showcaseSlides.length;
};

const previousSlide = () => {
    activeSlide.value =
        (activeSlide.value - 1 + showcaseSlides.length) % showcaseSlides.length;
};

const startAutoSlide = () => {
    stopAutoSlide();
    slideInterval = setInterval(nextSlide, 5000);
};

const stopAutoSlide = () => {
    if (!slideInterval) {
        return;
    }

    clearInterval(slideInterval);
    slideInterval = null;
};

const handleTouchStart = (event: TouchEvent) => {
    touchStartX.value = event.touches[0]?.clientX ?? null;
};

const handleTouchEnd = (event: TouchEvent) => {
    if (touchStartX.value === null) {
        return;
    }

    const touchEndX = event.changedTouches[0]?.clientX ?? touchStartX.value;
    const delta = touchStartX.value - touchEndX;

    if (Math.abs(delta) > 35) {
        if (delta > 0) {
            nextSlide();
        } else {
            previousSlide();
        }
    }

    touchStartX.value = null;
};

const handleKeydown = (event: KeyboardEvent) => {
    if (event.key === 'ArrowRight') {
        nextSlide();
    }

    if (event.key === 'ArrowLeft') {
        previousSlide();
    }
};

onMounted(() => {
    startAutoSlide();
});

onBeforeUnmount(() => {
    stopAutoSlide();
});
</script>

<template>
    <Head title="Storify" />

    <div class="min-h-screen bg-background text-foreground">
        <header class="sticky top-0 z-30 border-b border-border/70 bg-background/90 backdrop-blur">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 sm:py-4">
                <div class="flex min-w-0 items-center gap-2.5 sm:gap-3">
                    <AppLogo />
                    <p class="hidden text-xs text-muted-foreground sm:block">Smart Ecommerce Management Made Simple</p>
                </div>
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-md border border-border bg-card px-3 py-2 text-xs hover:border-primary/40 hover:bg-muted sm:px-4 sm:text-sm"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-md border border-border bg-card px-2.5 py-2 text-xs text-muted-foreground hover:border-primary/35 hover:bg-muted sm:px-4 sm:text-sm"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="rounded-md bg-primary px-3 py-2 text-xs font-medium text-primary-foreground shadow-sm shadow-primary/20 hover:bg-accent sm:px-4 sm:text-sm"
                        >
                            Start Purchasing
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl space-y-14 px-4 py-8 sm:space-y-16 sm:px-6 sm:py-12">
            <section class="relative space-y-6 overflow-hidden rounded-2xl border border-border bg-gradient-to-br from-card via-card to-muted/50 p-5 shadow-2xl shadow-primary/5 sm:space-y-8 sm:p-8">
                <div class="pointer-events-none absolute -top-24 -right-12 h-48 w-48 rounded-full bg-primary/20 blur-3xl"></div>
                <div class="space-y-3 sm:space-y-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-primary sm:text-sm">Smart Ecommerce Platform</p>
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">
                        Storify
                    </h1>
                    <p class="text-base text-muted-foreground sm:text-lg">
                        Smart Ecommerce Management Made Simple
                    </p>
                    <p class="max-w-2xl text-sm leading-6 text-muted-foreground">
                        Manage products, process orders, monitor analytics, and improve customer shopping experience
                        from one clean and scalable platform.
                    </p>
                    <div class="flex flex-col gap-2 pt-2 sm:flex-row sm:items-center">
                        <Button as-child class="h-10 w-full shadow-sm shadow-primary/25 sm:w-auto">
                            <Link :href="$page.props.auth.user ? dashboard() : login()">Start Purchasing</Link>
                        </Button>
                        <Button as-child variant="secondary" class="h-10 w-full sm:w-auto">
                            <Link :href="login()">Log in</Link>
                        </Button>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-primary">Platform Preview</p>
                        <p class="text-xs text-muted-foreground">Explore Storify</p>
                    </div>
                    <div
                        class="relative isolate overflow-visible rounded-xl"
                        tabindex="0"
                        @mouseenter="stopAutoSlide"
                        @mouseleave="startAutoSlide"
                        @touchstart="handleTouchStart"
                        @touchend="handleTouchEnd"
                        @keydown="handleKeydown"
                    >
                        <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_50%_45%,rgba(29,185,84,0.26),rgba(29,185,84,0.08)_38%,transparent_72%)] blur-2xl"></div>
                        <div class="pointer-events-none absolute -inset-x-8 top-1/2 -z-10 h-24 -translate-y-1/2 bg-primary/15 blur-3xl"></div>
                        <div class="relative aspect-[4/5] overflow-hidden rounded-xl bg-transparent sm:aspect-[16/9]">
                            <img
                                v-for="(slide, index) in showcaseSlides"
                                :key="slide.src"
                                :src="slide.src"
                                :alt="slide.alt"
                                class="absolute inset-0 h-full w-full object-cover object-[74%_center] transition-all duration-500 ease-out sm:object-contain sm:object-center"
                                :class="index === activeSlide ? 'translate-x-0 opacity-100' : 'translate-x-3 opacity-0'"
                            />
                        </div>

                        <div class="absolute inset-x-2 top-1/2 flex -translate-y-1/2 items-center justify-between sm:inset-x-3">
                            <Button
                                type="button"
                                variant="secondary"
                                size="icon-sm"
                                class="h-7 w-7 rounded-full border border-white/15 bg-black/45 text-white backdrop-blur hover:border-primary/50 hover:bg-black/65 sm:h-8 sm:w-8"
                                @click="previousSlide"
                            >
                                <ChevronLeft class="h-4 w-4" />
                            </Button>
                            <Button
                                type="button"
                                variant="secondary"
                                size="icon-sm"
                                class="h-7 w-7 rounded-full border border-white/15 bg-black/45 text-white backdrop-blur hover:border-primary/50 hover:bg-black/65 sm:h-8 sm:w-8"
                                @click="nextSlide"
                            >
                                <ChevronRight class="h-4 w-4" />
                            </Button>
                        </div>

                        <div class="absolute inset-x-0 bottom-2 flex items-center justify-center gap-2 sm:bottom-3">
                            <button
                                v-for="(slide, index) in showcaseSlides"
                                :key="`dot-${slide.src}`"
                                type="button"
                                class="h-2 rounded-full transition-all"
                                :class="index === activeSlide ? 'w-6 bg-primary' : 'w-2 bg-white/40 hover:bg-white/60'"
                                :aria-label="`Go to slide ${index + 1}`"
                                @click="goToSlide(index)"
                            ></button>
                        </div>
                    </div>
                </div>

                <Card class="border-border/80 bg-card/90 shadow-lg shadow-primary/10 transition-all hover:border-primary/30">
                    <CardHeader class="space-y-1 pb-3">
                        <CardTitle class="flex items-start gap-2 text-base leading-tight sm:items-center sm:text-lg">
                            <Sparkles class="h-4 w-4 text-primary" />
                            Built for Smart Purchasing Online
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-2.5 pt-0 text-sm text-muted-foreground">
                        <p class="flex items-start gap-2"><CheckCircle2 class="mt-0.5 h-4 w-4 text-primary" />Easy product browsing and smart shopping experience</p>
                        <p class="flex items-start gap-2"><CheckCircle2 class="mt-0.5 h-4 w-4 text-primary" />Simple cart, checkout, and order tracking flow</p>
                        <p class="flex items-start gap-2"><CheckCircle2 class="mt-0.5 h-4 w-4 text-primary" />Helpful purchase insights and organized order management</p>
                    </CardContent>
                </Card>
            </section>

            <section class="space-y-6 pt-5 sm:pt-8">
                <h2 class="text-2xl font-semibold">Core Features</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="feature in features" :key="feature.title" class="border-border bg-card/90 transition-all hover:-translate-y-1 hover:border-primary/40 hover:shadow-lg hover:shadow-primary/10">
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
                    <Card class="bg-card/90">
                        <CardHeader><CardTitle class="flex items-center gap-2 text-base"><Store class="h-4 w-4 text-primary" />1. Setup Catalog</CardTitle></CardHeader>
                        <CardContent class="text-sm text-muted-foreground">
                            Add products with pricing, stock, descriptions, and images.
                        </CardContent>
                    </Card>
                    <Card class="bg-card/90">
                        <CardHeader><CardTitle class="flex items-center gap-2 text-base"><ClipboardList class="h-4 w-4 text-primary" />2. Process Orders</CardTitle></CardHeader>
                        <CardContent class="text-sm text-muted-foreground">
                            Customers place orders, admins track status, and fulfillment runs smoothly.
                        </CardContent>
                    </Card>
                    <Card class="bg-card/90">
                        <CardHeader><CardTitle class="flex items-center gap-2 text-base"><BarChart3 class="h-4 w-4 text-primary" />3. Optimize Growth</CardTitle></CardHeader>
                        <CardContent class="text-sm text-muted-foreground">
                            Use analytics insights to improve sales, stock planning, and customer experience.
                        </CardContent>
                    </Card>
                </div>
            </section>

            <section class="grid gap-4 lg:grid-cols-2">
                <Card class="bg-card/90">
                    <CardHeader><CardTitle class="flex items-center gap-2"><PackageSearch class="h-4 w-4 text-primary" />Admin Overview</CardTitle></CardHeader>
                    <CardContent class="space-y-2 text-sm text-muted-foreground">
                        <p>- Manage products and inventory in a clean dashboard</p>
                        <p>- Track and update order statuses with structured flow</p>
                        <p>- Monitor revenue, orders, best sellers, and low stock alerts</p>
                    </CardContent>
                </Card>
                <Card class="bg-card/90">
                    <CardHeader><CardTitle class="flex items-center gap-2"><ShoppingBag class="h-4 w-4 text-primary" />Customer Experience</CardTitle></CardHeader>
                    <CardContent class="space-y-2 text-sm text-muted-foreground">
                        <p>- Discover products with responsive shopping interface</p>
                        <p>- Checkout with clear steps and shipping/payment support</p>
                        <p>- View order history and delivery tracking in one place</p>
                    </CardContent>
                </Card>
            </section>

            <section class="rounded-xl border border-border bg-gradient-to-br from-card to-muted/40 p-8 text-center shadow-lg shadow-primary/5">
                <h2 class="text-2xl font-semibold">Launch and Scale with Storify</h2>
                <p class="mx-auto mt-2 max-w-2xl text-sm text-muted-foreground">
                    Move from basic storefront to complete ecommerce management with a modern, production-ready system.
                </p>
                <Button class="mt-4" as-child>
                    <Link :href="$page.props.auth.user ? dashboard() : login()">Start Purchasing</Link>
                </Button>
            </section>
        </main>
    </div>
</template>
