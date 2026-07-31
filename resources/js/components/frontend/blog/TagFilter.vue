<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { localizedUrl } from '@/lib/localizedUrl';

type Tag = { id: number; name: string; permalink: string };

const props = defineProps<{
    tags: Tag[];
    activeSlug: string | null;
    /** Currently-active category — preserved across tag clicks. */
    activeCategory?: string | null;
    /** Currently-active search term — preserved across tag clicks. */
    searchTerm?: string | null;
    locale: string;
    allLabel?: string;
}>();

// Build /blog?... preserving sibling filters. Mirrors CategoryFilter — the
// component only owns `tag`; `category` and `q` come from props so the user
// can layer filters without losing their other selections.
function hrefFor(tagSlug: string | null): string {
    const params = new URLSearchParams();
    if (tagSlug) params.set('tag', tagSlug);
    if (props.activeCategory) params.set('category', props.activeCategory);
    if (props.searchTerm) params.set('q', props.searchTerm);
    const qs = params.toString();
    return localizedUrl(props.locale, qs ? `/blog?${qs}` : '/blog');
}
</script>

<template>
    <div v-if="tags.length > 0" class="flex flex-wrap items-center gap-2">
        <Link
            :href="hrefFor(null)"
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
            :href="hrefFor(tag.permalink)"
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
