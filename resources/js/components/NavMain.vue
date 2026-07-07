<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
    label?: string;
}>();

const { isCurrentUrl } = useCurrentUrl();

const BRAND_BUTTON_CLASS = [
    'transition-colors hover:bg-[var(--orange)]/8 hover:text-[var(--orange)]',
    'data-[active=true]:bg-[var(--orange)]/12 data-[active=true]:text-[var(--orange)] data-[active=true]:font-semibold',
    'data-[active=true]:hover:bg-[var(--orange)]/18 data-[active=true]:hover:text-[var(--orange)]',
    'data-[active=true]:shadow-[inset_3px_0_0_var(--orange)]',
].join(' ');
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    :class="BRAND_BUTTON_CLASS"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
