<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight, Menu as MenuIcon } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Card, CardContent } from '@/components/ui/card';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.navigation_management'), href: '/admin/menus' },
]);

type MenuRow = {
    id: number;
    key: string;
    name: string;
    is_active: boolean;
    item_count: number;
};

defineProps<{
    menus: MenuRow[];
}>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});
</script>

<template>
    <Head :title="t('sidebar.navigation_management')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="t('sidebar.navigation_management')"
            :description="t('menus.description')"
        />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="menu in menus"
                :key="menu.id"
                :href="`/admin/menus/${menu.key}`"
                class="block"
            >
                <Card class="transition-shadow hover:shadow-md">
                    <CardContent class="flex items-center gap-4 p-5">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600 dark:bg-sky-900/40 dark:text-sky-300">
                            <MenuIcon class="size-6" />
                        </div>
                        <div class="flex-1">
                            <div class="text-base font-semibold">{{ menu.name }}</div>
                            <div class="text-xs text-muted-foreground">
                                Slot: <code class="rounded bg-muted px-1 py-0.5 font-mono text-[11px]">{{ menu.key }}</code>
                            </div>
                            <div class="mt-1 text-xs text-muted-foreground">
                                {{ menu.item_count }} item{{ menu.item_count === 1 ? '' : 's' }}
                            </div>
                        </div>
                        <ChevronRight class="size-4 text-muted-foreground" />
                    </CardContent>
                </Card>
            </Link>
        </div>
    </div>
</template>
