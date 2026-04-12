<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BarChart3, CreditCard, LayoutGrid, Package, ReceiptText, ShoppingCart } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as adminProductsIndex } from '@/routes/admin/products';
import { index as customerCartIndex } from '@/routes/customer/cart';
import { index as adminAnalyticsIndex } from '@/routes/admin/analytics';
import { index as adminOrdersIndex } from '@/routes/admin/orders';
import { index as customerCheckoutIndex } from '@/routes/customer/checkout';
import { index as customerOrdersIndex } from '@/routes/customer/orders';
import { index as customerProductsIndex } from '@/routes/customer/products';
import type { NavItem } from '@/types';

const page = usePage();
const userRole = computed(() => page.props.auth.user.role as string);
const sidebarClass = computed(() =>
    userRole.value === 'customer'
        ? 'bg-sidebar text-sidebar-foreground hidden md:flex'
        : 'bg-sidebar text-sidebar-foreground',
);

const mainNavItems = computed<NavItem[]>(() =>
    userRole.value === 'admin'
        ? [
              {
                  title: 'Dashboard',
                  href: dashboard(),
                  icon: LayoutGrid,
              },
              {
                  title: 'Manage Products',
                  href: adminProductsIndex(),
                  icon: Package,
              },
              {
                  title: 'Orders',
                  href: adminOrdersIndex(),
                  icon: ReceiptText,
              },
              {
                  title: 'Analytics',
                  href: adminAnalyticsIndex(),
                  icon: BarChart3,
              },
          ]
        : [
              {
                  title: 'Dashboard',
                  href: dashboard(),
                  icon: LayoutGrid,
              },
              {
                  title: 'Products',
                  href: customerProductsIndex(),
                  icon: Package,
              },
              {
                  title: 'Cart',
                  href: customerCartIndex(),
                  icon: ShoppingCart,
              },
              {
                  title: 'Checkout',
                  href: customerCheckoutIndex(),
                  icon: CreditCard,
              },
              {
                  title: 'Orders',
                  href: customerOrdersIndex(),
                  icon: ReceiptText,
              },
          ],
);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" :class="sidebarClass">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
