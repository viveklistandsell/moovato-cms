<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { localizedUrl } from '@/lib/localizedUrl';

type Category = { id: number; name: string; permalink: string };

defineProps<{
    categories: Category[];
    activeSlug: string | null;
    locale: string;
    allLabel?: string;
}>();
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <Link
            :href="localizedUrl(locale, '/blog')"
            class="rounded-full border px-4 py-1.5 text-sm font-medium transition-colors"
            :class="
                activeSlug === null
                    ? 'border-primary bg-primary text-primary-foreground'
                    : 'border-border text-muted-foreground hover:border-foreground hover:text-foreground'
            "
        >
            {{ allLabel ?? (locale === 'de' ? 'Alle' : 'All') }}
        </Link>
        <Link
            v-for="cat in categories"
            :key="cat.id"
            :href="localizedUrl(locale, `/blog?category=${cat.permalink}`)"
            class="rounded-full border px-4 py-1.5 text-sm font-medium transition-colors"
            :class="
                activeSlug === cat.permalink
                    ? 'border-primary bg-primary text-primary-foreground'
                    : 'border-border text-muted-foreground hover:border-foreground hover:text-foreground'
            "
        >
            {{ cat.name }}
        </Link>
    </div>
</template>
