<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ChevronLeft, ChevronRight, Search, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import CategoryFilter from '@/components/frontend/blog/CategoryFilter.vue';
import FeaturedPost from '@/components/frontend/blog/FeaturedPost.vue';
import PostCard from '@/components/frontend/blog/PostCard.vue';
import TagFilter from '@/components/frontend/blog/TagFilter.vue';
import { localizedUrl } from '@/lib/localizedUrl';

type Category = { id: number; name: string; permalink: string };
type Tag = { id: number; name: string; permalink: string };

type Post = {
    id: number;
    title: string;
    permalink: string;
    excerpt: string | null;
    image_url: string | null;
    author: string | null;
    created_at: string | null;
    reading_time: number;
    is_sticky: boolean;
    is_featured: boolean;
    categories: Category[];
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

const props = defineProps<{
    locale: string;
    featured: Post | null;
    posts: Post[];
    categories: Category[];
    tags: Tag[];
    activeCategory: string | null;
    activeTag: string | null;
    searchTerm: string;
    pagination: {
        current_page: number;
        last_page: number;
        total: number;
        links: PaginationLink[];
    };
}>();

const t = computed(() => ({
    home: props.locale === 'de' ? 'Startseite' : 'Home',
    blog: 'Blogs',
    overview:
        props.locale === 'de'
            ? 'Blog-Artikel in der Übersicht:'
            : 'Blog articles overview:',
    empty:
        props.locale === 'de' ? 'Keine Beiträge gefunden.' : 'No posts found.',
    'Filter by Category':
        props.locale === 'de'
            ? 'Nach Kategorie filtern:'
            : 'Filter by category:',
    'Filter by Tag':
        props.locale === 'de'            
            ? 'Nach Schlagwort filtern:'
            : 'Filter by tag:',
}));

function isPrev(label: string): boolean {
    return (
        label.toLowerCase().includes('previous') || label.includes('&laquo;')
    );
}
function isNext(label: string): boolean {
    return label.toLowerCase().includes('next') || label.includes('&raquo;');
}
function cleanLabel(label: string): string {
    return label.replace(/&laquo;|&raquo;|Previous|Next/gi, '').trim();
}
</script>

<template>
    <Head title="Blog" />

    <div class="mx-auto max-w-6xl px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center gap-2 text-lg text-muted-foreground">
            <Link
                :href="localizedUrl(locale, '/')"
                class="hover:text-foreground"
            >
                {{ t.home }}
            </Link>
            <span>›</span>
            <span class="text-foreground">{{ t.blog }}</span>
        </nav>

        <!-- Featured / latest -->
        <FeaturedPost
            v-if="featured"
            :post="featured"
            :locale="locale"
            class="mb-10"
        />
        <!-- Search input -->
        <div class="flex items-center justify-end">
            <div class="relative w-full max-w-sm">
                <Search
                    class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <input
                    :value="searchInput"
                    type="text"
                    :placeholder="t.searchPlaceholder"
                    class="h-10 w-full rounded-md border border-input bg-background pl-9 pr-9 text-sm shadow-sm transition placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                    @input="onSearchInput"
                />
                <button
                    v-if="searchInput.length > 0"
                    type="button"
                    class="absolute right-2 top-1/2 inline-flex size-6 -translate-y-1/2 items-center justify-center rounded-sm text-muted-foreground hover:bg-muted hover:text-foreground"
                    :aria-label="t.empty"
                    @click="clearSearch"
                >
                    <X class="size-3.5" />
                </button>
            </div>
        </div>

        <!-- Search-results header (replaces hero while ?q= is active) -->
        <div v-if="searchTerm" class="mb-8">
            <h2 class="text-2xl font-bold tracking-tight">
                {{ t.searchResultsFor }}: “{{ searchTerm }}”
            </h2>
            <p class="mt-1 text-sm text-muted-foreground">
                {{ pagination.total }}
            </p>
        </div>

        <!-- Category filter chips -->
        <span
            v-if="categories.length > 0"
            class="mb-4 block text-sm font-semibold tracking-wide text-muted-foreground uppercase"
            >{{ t['Filter by Category'] }}</span
        >
        <CategoryFilter
            :categories="categories"
            :active-slug="activeCategory"
            :locale="locale"
            class="mb-4"
        />

        <!-- Tag filter chips -->
        <span
            v-if="tags.length > 0"
            class="mb-4 block text-sm font-semibold tracking-wide text-muted-foreground uppercase"
            >{{ t['Filter by Tag'] }}</span
        >
        <TagFilter
            :tags="tags"
            :active-slug="activeTag"
            :locale="locale"
            class="mb-8 border-b border-border/60 pb-6"
        />

        <!-- Section title -->
        <h2 class="mb-6 text-2xl font-bold tracking-tight">
            {{ t.overview }}
        </h2>

        <!-- Empty state -->
        <div
            v-if="posts.length === 0"
            class="rounded-lg border border-dashed py-16 text-center text-sm text-muted-foreground"
        >
            {{ t.empty }}
        </div>

        <!-- Grid -->
        <div v-else class="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
            <PostCard
                v-for="post in posts"
                :key="post.id"
                :post="post"
                :locale="locale"
            />
        </div>

        <!-- Pagination -->
        <div
            v-if="pagination.last_page > 1"
            class="mt-12 flex items-center justify-center gap-1"
        >
            <template v-for="(link, idx) in pagination.links" :key="idx">
                <Link
                    v-if="isPrev(link.label) && link.url"
                    :href="link.url"
                    class="inline-flex h-9 cursor-pointer items-center rounded-md border px-3 text-sm hover:bg-muted"
                >
                    <ChevronLeft class="size-4" />
                </Link>
                <span
                    v-else-if="isPrev(link.label)"
                    class="inline-flex h-9 cursor-not-allowed items-center rounded-md border px-3 text-sm opacity-40"
                >
                    <ChevronLeft class="size-4" />
                </span>

                <Link
                    v-else-if="isNext(link.label) && link.url"
                    :href="link.url"
                    class="inline-flex h-9 cursor-pointer items-center rounded-md border px-3 text-sm hover:bg-muted"
                >
                    <ChevronRight class="size-4" />
                </Link>
                <span
                    v-else-if="isNext(link.label)"
                    class="inline-flex h-9 cursor-not-allowed items-center rounded-md border px-3 text-sm opacity-40"
                >
                    <ChevronRight class="size-4" />
                </span>

                <Link
                    v-else-if="link.url && !link.active"
                    :href="link.url"
                    class="inline-flex h-9 min-w-9 cursor-pointer items-center justify-center rounded-md border px-2 text-sm hover:bg-muted"
                >
                    <span v-html="cleanLabel(link.label) || link.label" />
                </Link>
                <span
                    v-else
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-2 text-sm"
                    :class="
                        link.active
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'cursor-not-allowed opacity-40'
                    "
                >
                    <span v-html="cleanLabel(link.label) || link.label" />
                </span>
            </template>
        </div>
    </div>
</template>
