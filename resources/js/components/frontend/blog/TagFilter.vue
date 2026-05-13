<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

type Tag = { id: number; name: string; permalink: string };

defineProps<{
    tags: Tag[];
    activeSlug: string | null;
    locale: string;
    allLabel?: string;
}>();
</script>

<template>
    <div v-if="tags.length > 0" class="flex flex-wrap items-center gap-2">
        <Link
            :href="`/${locale}/blog`"
            class="rounded-full border px-3 py-1 text-xs font-medium transition-colors"
            :class="
                activeSlug === null
                    ? 'border-primary bg-primary text-primary-foreground'
                    : 'border-border text-muted-foreground hover:border-foreground hover:text-foreground'
            "
        >
            #{{ allLabel ?? (locale === 'de' ? 'Alle' : 'All') }}
        </Link>
        <Link
            v-for="tag in tags"
            :key="tag.id"
            :href="`/${locale}/blog?tag=${tag.permalink}`"
            class="rounded-full border px-3 py-1 text-xs font-medium transition-colors"
            :class="
                activeSlug === tag.permalink
                    ? 'border-primary bg-primary text-primary-foreground'
                    : 'border-border text-muted-foreground hover:border-foreground hover:text-foreground'
            "
        >
            #{{ tag.name }}
        </Link>
    </div>
</template>
