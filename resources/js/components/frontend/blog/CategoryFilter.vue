<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { localizedUrl } from '@/lib/localizedUrl';

type Category = { id: number; name: string; permalink: string };

const props = defineProps<{
    categories: Category[];
    activeSlug: string | null;
    /** Currently-active tag — preserved across category clicks. */
    activeTag?: string | null;
    /** Currently-active search term — preserved across category clicks. */
    searchTerm?: string | null;
    locale: string;
    allLabel?: string;
}>();

// Build /blog?... preserving sibling filters. `category` is the only field
// this component owns; `tag` and `q` come from props so a click here doesn't
// silently reset them. Pass `null` for category to mean "show all categories"
// while keeping tag + search intact.
function hrefFor(categorySlug: string | null): string {
    const params = new URLSearchParams();
    if (categorySlug) params.set('category', categorySlug);
    if (props.activeTag) params.set('tag', props.activeTag);
    if (props.searchTerm) params.set('q', props.searchTerm);
    const qs = params.toString();
    return localizedUrl(props.locale, qs ? `/blog?${qs}` : '/blog');
}
</script>

<template>
    <div v-if="categories.length > 0" class="flex flex-wrap items-center gap-2">
        <Link :href="hrefFor(null)" class="rounded-full border px-4 py-1.5 text-sm font-medium transition-colors"
            :class="activeSlug === null
                    ? 'border-primary bg-primary text-primary-foreground'
                    : 'border-border text-muted-foreground hover:border-foreground hover:text-foreground'
                ">
            {{ allLabel ?? (locale === 'de' ? 'Alle' : 'All') }}
        </Link>
        <Link v-for="cat in categories" :key="cat.id" :href="hrefFor(cat.permalink)"
            class="rounded-full border px-4 py-1.5 text-sm font-medium transition-colors" :class="activeSlug === cat.permalink
                    ? 'border-primary bg-primary text-primary-foreground'
                    : 'border-border text-muted-foreground hover:border-foreground hover:text-foreground'
                ">
            {{ cat.name }}
        </Link>
    </div>
</template>
