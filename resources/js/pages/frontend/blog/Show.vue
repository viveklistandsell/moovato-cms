<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, Clock, Eye, List } from 'lucide-vue-next';
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';
import PostCard from '@/components/frontend/blog/PostCard.vue';
import { localizedUrl } from '@/lib/localizedUrl';

type TocEntry = { id: string; text: string; level: number };

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
    seo?: {
        meta_title: string | null;
        meta_description: string | null;
        schema: Record<string, unknown> | null;
        meta_image_url: string | null;
    };
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
    toc: props.locale === 'de' ? 'Inhaltsverzeichnis' : 'Table of Contents',
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

const documentTitle = computed(() => props.post.seo?.meta_title || props.post.title);
const seoDescription = computed(
    () => props.post.seo?.meta_description || props.post.excerpt || '',
);
const seoImage = computed(
    () => props.post.seo?.meta_image_url || props.post.image_url || null,
);
const seoSchemaJson = computed<string | null>(() => {
    const s = props.post.seo?.schema;
    if (! s || typeof s !== 'object' || Object.keys(s).length === 0) return null;
    try {
        return JSON.stringify(s);
    } catch {
        return null;
    }
});

const contentRef = ref<HTMLElement | null>(null);
const toc = ref<TocEntry[]>([]);
const activeId = ref<string | null>(null);
let observer: IntersectionObserver | null = null;

function slugify(input: string): string {
    return input
        .toLowerCase()
        .normalize('NFKD')
        .replace(/[̀-ͯ]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .slice(0, 80);
}

function buildToc(): void {
    const root = contentRef.value;
    if (!root) {
        toc.value = [];
        return;
    }

    const headings = Array.from(
        root.querySelectorAll<HTMLHeadingElement>('h1, h2, h3, h4, h5, h6'),
    );

    const entries: TocEntry[] = [];
    const used = new Set<string>();

    for (const heading of headings) {
        const text = (heading.textContent ?? '').trim();
        if (text === '') {
            continue;
        }

        const base = heading.id || slugify(text) || `heading-${entries.length}`;
        let id = base;
        let suffix = 2;
        while (used.has(id)) {
            id = `${base}-${suffix++}`;
        }

        heading.id = id;
        heading.classList.add('scroll-mt-24');
        used.add(id);

        entries.push({
            id,
            text,
            level: Number(heading.tagName.substring(1)),
        });
    }

    toc.value = entries;
}

function setupObserver(): void {
    if (typeof IntersectionObserver === 'undefined') {
        return;
    }
    observer?.disconnect();
    const root = contentRef.value;
    if (!root) {
        return;
    }

    observer = new IntersectionObserver(
        (entries) => {
            const visible = entries
                .filter((e) => e.isIntersecting)
                .sort(
                    (a, b) =>
                        a.target.getBoundingClientRect().top -
                        b.target.getBoundingClientRect().top,
                );
            if (visible.length > 0) {
                activeId.value = (visible[0].target as HTMLElement).id;
            }
        },
        { rootMargin: '-80px 0px -65% 0px', threshold: 0 },
    );

    root.querySelectorAll<HTMLHeadingElement>('h1, h2, h3, h4, h5, h6').forEach(
        (h) => observer!.observe(h),
    );
}

function scrollToHeading(id: string, event: Event): void {
    event.preventDefault();
    const el = document.getElementById(id);
    if (!el) {
        return;
    }
    const header = document.querySelector('header');
    const headerHeight = header ? header.getBoundingClientRect().height : 0;
    const y =
        el.getBoundingClientRect().top + window.scrollY - headerHeight - 16;
    window.scrollTo({ top: y, behavior: 'smooth' });
    activeId.value = id;
    history.replaceState(null, '', `#${id}`);
}

async function refreshToc(): Promise<void> {
    await nextTick();
    buildToc();
    setupObserver();
}

onMounted(refreshToc);
watch(() => props.post.content, refreshToc);
onBeforeUnmount(() => observer?.disconnect());
</script>

<template>
    <Head>
        <title>{{ documentTitle }}</title>
        <meta
            v-if="seoDescription"
            head-key="description"
            name="description"
            :content="seoDescription"
        />
        <meta
            head-key="og:title"
            property="og:title"
            :content="documentTitle"
        />
        <meta
            v-if="seoDescription"
            head-key="og:description"
            property="og:description"
            :content="seoDescription"
        />
        <meta
            v-if="seoImage"
            head-key="og:image"
            property="og:image"
            :content="seoImage"
        />
        <component
            :is="'script'"
            v-if="seoSchemaJson"
            head-key="schema-ld"
            type="application/ld+json"
            v-html="seoSchemaJson"
        />
    </Head>

    <div class="container-xl section-py">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-muted-foreground">
            <Link
                :href="localizedUrl(locale, '/')"
                class="hover:text-foreground"
            >
                {{ t.home }}
            </Link>
            <span>›</span>
            <Link
                :href="localizedUrl(locale, '/blog')"
                class="hover:text-foreground"
            >
                {{ t.blog }}
            </Link>
            <span>›</span>
            <span class="line-clamp-1 text-foreground">{{ post.title }}</span>
        </nav>

        <div
            :class="
                toc.length > 0
                    ? 'lg:grid lg:grid-cols-[260px_minmax(0,1fr)] lg:gap-10'
                    : ''
            "
        >
            <!-- Table of Contents (sticky on lg+) -->
            <aside v-if="toc.length > 0" class="hidden lg:block">
                <div
                    class="sticky top-20 max-h-[calc(100vh-6rem)] overflow-y-auto pr-2"
                >
                    <div
                        class="mb-3 inline-flex items-center gap-2 text-2xl font-extrabold text-foreground"
                    >
                        <List class="size-4 text-primary" />
                        {{ t.toc }}
                    </div>
                    <nav class="flex flex-col text-sm">
                        <a
                            v-for="entry in toc"
                            :key="entry.id"
                            :href="`#${entry.id}`"
                            :class="[
                                'block border-l-2 py-1.5 leading-snug transition-colors hover:text-foreground',
                                entry.level <= 2
                                    ? 'pl-3'
                                    : entry.level === 3
                                      ? 'pl-6'
                                      : entry.level === 4
                                        ? 'pl-9'
                                        : 'pl-12',
                                activeId === entry.id
                                    ? 'border-primary font-medium text-foreground'
                                    : 'border-transparent text-muted-foreground hover:border-border',
                            ]"
                            @click="scrollToHeading(entry.id, $event)"
                        >
                            {{ entry.text }}
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Article body -->
            <article class="min-w-0">
                <!-- Categories -->
                <div
                    v-if="post.categories.length > 0"
                    class="flex flex-wrap gap-2 text-sm font-extrabold tracking-wider  text-[var(--orange)] uppercase dark:text-[var(--orange)]"
                >
                    <Link
                        v-for="cat in post.categories"
                        :key="cat.id"
                        :href="
                            localizedUrl(
                                locale,
                                `/blog?category=${cat.permalink}`,
                            )
                        "
                        class="hover:underline"
                    >
                        {{ cat.name }}
                    </Link>
                </div>

                <!-- Title -->
                <h1
                    class="mb-2 text-3xl leading-tight font-extrabold tracking-tight sm:text-4xl md:text-5xl"
                >
                    {{ post.title }}
                </h1>

                <!-- Meta row -->
                <div
                    class="mb-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-muted-foreground"
                >
                    <span v-if="post.author">{{ t.by }} {{ post.author }}</span>
                    <span
                        v-if="formattedDate"
                        class="inline-flex items-center gap-1.5"
                    >
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
                    class="mb-5 overflow-hidden rounded-xl bg-muted"
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
                    class="mb-8 border-l-4 border-primary/40 bg-muted/30 px-4 py-3 text-base leading-relaxed text-muted-foreground italic"
                >
                    {{ post.excerpt }}
                </p>

                <!-- Content -->
                <div
                    v-if="post.content"
                    ref="contentRef"
                    class="prose prose-neutral dark:prose-invert prose-headings:font-bold prose-headings:scroll-mt-24 prose-a:text-primary prose-img:rounded-md max-w-none"
                    v-html="post.content"
                />

                <!-- Tags -->
                <div
                    v-if="post.tags.length > 0"
                    class="mt-5 flex flex-wrap gap-2 border-t border-border/60 pt-6"
                >
                    <Link
                        v-for="tag in post.tags"
                        :key="tag.id"
                        :href="
                            localizedUrl(locale, `/blog?tag=${tag.permalink}`)
                        "
                        class="rounded-full border border-border px-3 py-1 text-xs text-muted-foreground hover:border-foreground hover:text-foreground"
                    >
                        #{{ tag.name }}
                    </Link>
                </div>

                <!-- Back link -->
                <div class="mt-10">
                    <Link
                        :href="localizedUrl(locale, '/blog')"
                        class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
                    >
                        <ArrowLeft class="size-4" />
                        {{ t.back }}
                    </Link>
                </div>
            </article>
        </div>
    </div>

    <!-- Related posts -->
    <section
        v-if="related.length > 0"
        class="border-t border-border/60 bg-muted/20"
    >
        <div class="container-xl section-py">
            <h2 class="mb-6 text-2xl font-extrabold tracking-tight">
                {{ t.related }}
            </h2>
            <div class="grid gap-x-4 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
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
