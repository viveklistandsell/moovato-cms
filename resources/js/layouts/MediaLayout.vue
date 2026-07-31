<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, ChevronsUpDown } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import UserInfo from '@/components/UserInfo.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Toaster } from '@/components/ui/sonner';
import { useT } from '@/composables/useT';
import type { BreadcrumbItem } from '@/types';

const t = useT();

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background">
        <header
            class="flex h-14 shrink-0 items-center justify-between border-b border-border px-4 md:px-6"
        >
            <div class="flex items-center gap-3">
                <Link
                    href="/dashboard"
                    class="flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground"
                >
                    <ArrowLeft class="h-4 w-4" />
                    <span>{{ t('media.dashboard_back') }}</span>
                </Link>
                <span class="h-5 w-px bg-border"></span>
                <Link href="/dashboard" class="flex items-center gap-2">
                    <AppLogoIcon class="size-5 fill-current" />
                    <span class="text-sm font-semibold">Moovato CMS</span>
                </Link>
                <template v-if="breadcrumbs && breadcrumbs.length > 0">
                    <span class="h-5 w-px bg-border"></span>
                    <Breadcrumbs :breadcrumbs="breadcrumbs" />
                </template>
            </div>
            <DropdownMenu>
                <DropdownMenuTrigger
                    class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-accent"
                >
                    <UserInfo :user="user" />
                    <ChevronsUpDown class="size-4 text-muted-foreground" />
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="min-w-56 rounded-lg"
                    align="end"
                    :side-offset="4"
                >
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </header>

        <main class="flex flex-1 flex-col">
            <slot />
        </main>

        <Toaster />
    </div>
</template>
