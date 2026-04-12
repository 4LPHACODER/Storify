<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Package, ReceiptText, ShoppingCart, UserCircle2 } from 'lucide-vue-next';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { dashboard } from '@/routes';
import { index as customerCartIndex } from '@/routes/customer/cart';
import { index as customerOrdersIndex } from '@/routes/customer/orders';
import { index as customerProductsIndex } from '@/routes/customer/products';
import { edit as profileEdit } from '@/routes/profile';

const { isCurrentOrParentUrl } = useCurrentUrl();

const navItems = [
    { label: 'Home', href: dashboard(), icon: LayoutGrid },
    { label: 'Products', href: customerProductsIndex(), icon: Package },
    { label: 'Cart', href: customerCartIndex(), icon: ShoppingCart },
    { label: 'Orders', href: customerOrdersIndex(), icon: ReceiptText },
    { label: 'Profile', href: profileEdit(), icon: UserCircle2 },
] as const;
</script>

<template>
    <div class="pointer-events-none fixed inset-x-0 bottom-4 z-40 flex justify-center px-3 md:hidden">
        <nav
            class="pointer-events-auto flex w-full max-w-md items-center justify-between gap-1 rounded-2xl border border-border/80 bg-card/95 p-2 shadow-2xl shadow-black/35 backdrop-blur"
            aria-label="Customer mobile navigation"
        >
            <Link
                v-for="item in navItems"
                :key="item.label"
                :href="item.href"
                class="group flex min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-xl px-1 py-1.5 text-[11px] font-medium text-muted-foreground transition"
                :class="isCurrentOrParentUrl(item.href) ? 'bg-primary/20 text-primary' : 'hover:bg-muted/70 hover:text-foreground'"
            >
                <component :is="item.icon" class="h-4 w-4 shrink-0" />
                <span class="truncate">{{ item.label }}</span>
            </Link>
        </nav>
    </div>
</template>
