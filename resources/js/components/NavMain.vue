<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Zap } from 'lucide-vue-next';
import { computed } from 'vue';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { useT } from '@/composables/useT';
import type { NavItem } from '@/types';

const props = withDefaults(
    defineProps<{
        items: NavItem[];
        label?: string;
    }>(),
    { label: 'Platform' },
);

const t = useT();
const { isCurrentUrl } = useCurrentUrl();

// Translate the section label — defaults to t('sidebar.platform') if
// the caller didn't pass an override. Lets us keep one source of truth
// for "Platform" / "Plattform".
const sectionLabel = computed<string>(() => {
    if (props.label && props.label !== 'Platform') {
        return props.label;
    }
    return t('sidebar.platform');
});
const BRAND_BUTTON_CLASS = [
    'transition-colors hover:bg-[var(--orange)]/8 hover:text-[var(--orange)]',
    'data-[active=true]:bg-[var(--orange)]/12 data-[active=true]:text-[var(--orange)] data-[active=true]:font-semibold',
    'data-[active=true]:hover:bg-[var(--orange)]/18 data-[active=true]:hover:text-[var(--orange)]',
    'data-[active=true]:shadow-[inset_3px_0_0_var(--orange)]',
].join(' ');
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel
            class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-[var(--orange)]"
        >
            <Zap class="size-3" />
            {{ sectionLabel }}
        </SidebarGroupLabel>
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
