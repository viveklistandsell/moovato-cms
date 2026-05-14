<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { localizedUrl } from '@/lib/localizedUrl';

const props = defineProps<{
    locale: string;
}>();

const year = new Date().getFullYear();

const navItems = computed(() => [
    {
        label: props.locale === 'de' ? 'Startseite' : 'Home',
        href: localizedUrl(props.locale, '/'),
    },
    {
        label: 'Blog',
        href: localizedUrl(props.locale, '/blog'),
    },
    {
        label: props.locale === 'de' ? 'Seiten' : 'Pages',
        href: localizedUrl(props.locale, '/pages'),
    },
]);
</script>

<template>
    <footer class="border-t border-border/60 bg-muted/30">
        <div
            class="mx-auto flex max-w-6xl flex-col items-start justify-between gap-6 px-4 py-10 sm:flex-row sm:items-center"
        >
            <div>
                <p class="text-sm font-semibold">Moovato</p>
                <p class="mt-1 text-lg text-muted-foreground">
                    © {{ year }} Moovato. All rights reserved.
                </p>
            </div>
            <nav class="flex flex-wrap gap-x-6 gap-y-2 text-lg">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="text-muted-foreground hover:text-foreground"
                >
                    {{ item.label }}
                </Link>
            </nav>
        </div>
    </footer>
</template>
