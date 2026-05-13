<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, Clock, Eye } from 'lucide-vue-next';
import { computed } from 'vue';
import PostCard from '@/components/frontend/blog/PostCard.vue';

type Category = { id: number; name: string; permalink: string };
type Tag = { id: number; name: string; permalink: string };

type Post = {
    id: number;
    title: string;
    permalink: string;
    excerpt: string | null;
    content: string | null;
    image_url: string | null;
    author: string | null;
    created_at: string | null;
    reading_time: number;
    view_count: number;
    categories: Category[];
    tags: Tag[];
};

type RelatedPost = {
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

const props = defineProps<{
    locale: string;
    post: Post;
    related: RelatedPost[];
}>();

const t = computed(() => ({
    home: props.locale === 'de' ? 'Startseite' : 'Home',
    blog: 'Blogs',
    back: props.locale === 'de' ? 'Zurück zum Blog' : 'Back to blog',
    related: props.locale === 'de' ? 'Ähnliche Beiträge' : 'Related posts',
    by: props.locale === 'de' ? 'von' : 'by',
    minRead: props.locale === 'de' ? 'Min. Lesezeit' : 'min read',
    views: props.locale === 'de' ? 'Aufrufe' : 'views',
}));

const formattedDate = computed(() => {
    if (!props.post.created_at) {
        return '';
    }
    return new Date(props.post.created_at).toLocaleDateString(
        props.locale === 'de' ? 'de-DE' : 'en-US',
        { day: 'numeric', month: 'long', year: 'numeric' },
    );
});

const documentTitle = computed(() => props.post.title);
</script>

<template>
    <Head>
        <title>{{ documentTitle }}</title>
        <meta head-key="og:title" property="og:title" :content="documentTitle" />
        <meta
            v-if="post.image_url"
            head-key="og:image"
            property="og:image"
            :content="post.image_url"
        />
    </Head>

    <article class="mx-auto max-w-6xl px-4 py-10">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-muted-foreground">
            <Link :href="`/${locale}`" class="hover:text-foreground">
                {{ t.home }}
            </Link>
            <span>›</span>
            <Link :href="`/${locale}/blog`" class="hover:text-foreground">
                {{ t.blog }}
            </Link>
            <span>›</span>
            <span class="line-clamp-1 text-foreground">{{ post.title }}</span>
        </nav>

        <!-- Categories -->
        <div
            v-if="post.categories.length > 0"
            class="mb-3 flex flex-wrap gap-2 text-sm font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400"
        >
            <Link
                v-for="cat in post.categories"
                :key="cat.id"
                :href="`/${locale}/blog?category=${cat.permalink}`"
                class="hover:underline"
            >
                {{ cat.name }}
            </Link>
        </div>

        <!-- Title -->
        <h1
            class="mb-4 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl md:text-5xl"
        >
            {{ post.title }}
        </h1>

        <!-- Meta row -->
        <div
            class="mb-8 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-muted-foreground"
        >
            <span v-if="post.author">{{ t.by }} {{ post.author }}</span>
            <span v-if="formattedDate" class="inline-flex items-center gap-1.5">
                <Calendar class="size-4" />
                {{ formattedDate }}
            </span>
            <span
                v-if="post.reading_time"
                class="inline-flex items-center gap-1.5"
            >
                <Clock class="size-4" />
                {{ post.reading_time }} {{ t.minRead }}
            </span>
            <span class="inline-flex items-center gap-1.5">
                <Eye class="size-4" />
                {{ post.view_count.toLocaleString() }} {{ t.views }}
            </span>
        </div>

        <!-- Featured image -->
        <div
            v-if="post.image_url"
            class="mb-10 overflow-hidden rounded-xl bg-muted"
        >
            <img
                :src="post.image_url"
                :alt="post.title"
                class="aspect-[16/9] w-full object-cover"
            />
        </div>

        <!-- Short Description/Excerpt as lead-in -->
        <p
            v-if="post.excerpt"
            class="mb-8 border-l-4 border-primary/40 bg-muted/30 px-4 py-3 text-base italic leading-relaxed text-muted-foreground"
        >
            {{ post.excerpt }}
        </p>

        <!-- Content -->
        <div
            v-if="post.content"
            class="prose prose-neutral max-w-none dark:prose-invert prose-headings:font-bold prose-a:text-primary prose-img:rounded-md"
            v-html="post.content"
        />

        <!-- Tags -->
        <div
            v-if="post.tags.length > 0"
            class="mt-12 flex flex-wrap gap-2 border-t border-border/60 pt-6"
        >
            <Link
                v-for="tag in post.tags"
                :key="tag.id"
                :href="`/${locale}/blog?tag=${tag.permalink}`"
                class="rounded-full border border-border px-3 py-1 text-xs text-muted-foreground hover:border-foreground hover:text-foreground"
            >
                #{{ tag.name }}
            </Link>
        </div>

        <!-- Back link -->
        <div class="mt-10">
            <Link
                :href="`/${locale}/blog`"
                class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
            >
                <ArrowLeft class="size-4" />
                {{ t.back }}
            </Link>
        </div>
    </article>

    <!-- Related posts -->
    <section
        v-if="related.length > 0"
        class="border-t border-border/60 bg-muted/20"
    >
        <div class="mx-auto max-w-6xl px-4 py-12">
            <h2 class="mb-6 text-xl font-bold tracking-tight">
                {{ t.related }}
            </h2>
            <div class="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                <PostCard
                    v-for="rel in related"
                    :key="rel.id"
                    :post="rel"
                    :locale="locale"
                />
            </div>
        </div>
    </section>
</template>
