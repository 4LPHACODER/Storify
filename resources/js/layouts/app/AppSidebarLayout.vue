<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppContent from '@/components/AppContent.vue';
import CustomerMobileNav from '@/components/CustomerMobileNav.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage<{ auth?: { user?: { role?: string } } }>();
const isCustomer = computed(() => page.props.auth?.user?.role === 'customer');
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" :class="isCustomer ? 'overflow-x-hidden pb-24 md:pb-0' : 'overflow-x-hidden'">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
        <CustomerMobileNav v-if="isCustomer" />
        <Toaster />
    </AppShell>
</template>
