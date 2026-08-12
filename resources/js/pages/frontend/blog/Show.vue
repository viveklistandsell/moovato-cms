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

const documentTitle = computed(() => props.post.title);

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
            head-key="og:title"
            property="og:title"
            :content="documentTitle"
        />
        <meta
            v-if="post.image_url"
            head-key="og:image"
            property="og:image"
            :content="post.image_url"
        />
    </Head>

    <div class="container-xl section-py mv-blog-index">
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
                    class="mv-blogpost-toc sticky top-20 max-h-[calc(100vh-6rem)] overflow-y-auto rounded-2xl p-5"
                >
                    <div
                        class="mb-3 inline-flex items-center gap-2 text-lg font-extrabold text-foreground"
                    >
                        <List class="size-4 text-[var(--orange)]" />
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
                                    ? 'border-[var(--orange)] font-medium text-foreground'
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
                    class="mb-3 flex flex-wrap gap-2"
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
                        class="inline-flex items-center rounded-full bg-[var(--orange-soft)] px-3 py-1 text-[11px] font-bold tracking-wider text-[var(--orange)] uppercase transition-colors hover:bg-[var(--orange)] hover:text-white"
                    >
                        {{ cat.name }}
                    </Link>
                </div>

                <!-- Title -->
                <h1
                    class="mb-4 text-3xl leading-tight font-extrabold tracking-tight sm:text-4xl md:text-5xl"
                >
                    {{ post.title }}
                </h1>

                <!-- Meta row -->
                <div
                    class="mb-6 flex flex-wrap items-center gap-2 text-sm text-muted-foreground"
                >
                    <span v-if="post.author" class="mv-blogpost-chip">{{
                        t.by
                    }}
                        {{ post.author }}</span
                    >
                    <span
                        v-if="formattedDate"
                        class="mv-blogpost-chip"
                    >
                        <Calendar class="size-3.5 text-[var(--orange)]" />
                        {{ formattedDate }}
                    </span>
                    <span
                        v-if="post.reading_time"
                        class="mv-blogpost-chip"
                    >
                        <Clock class="size-3.5 text-[var(--orange)]" />
                        {{ post.reading_time }} {{ t.minRead }}
                    </span>
                    <span class="mv-blogpost-chip">
                        <Eye class="size-3.5 text-[var(--orange)]" />
                        {{ post.view_count.toLocaleString() }} {{ t.views }}
                    </span>
                </div>

                <!-- Featured image -->
                <div
                    v-if="post.image_url"
                    class="mv-blogpost-media mb-6 rounded-[28px] p-1.5"
                >
                    <div
                        class="mv-blogpost-media-inner overflow-hidden rounded-[22px] bg-muted"
                    >
                        <img
                            :src="post.image_url"
                            :alt="post.title"
                            class="aspect-[16/9] w-full object-cover"
                        />
                    </div>
                </div>

                <!-- Short Description/Excerpt as lead-in -->
                <p
                    v-if="post.excerpt"
                    class="mv-blogpost-lead mb-8 rounded-2xl border-l-4 px-5 py-4 text-base leading-relaxed italic"
                >
                    {{ post.excerpt }}
                </p>

                <!-- Content -->
                <div
                    v-if="post.content"
                    ref="contentRef"
                    class="mv-prose prose prose-neutral dark:prose-invert prose-headings:font-bold prose-headings:scroll-mt-24 prose-a:text-[var(--orange)] prose-img:rounded-md max-w-none"
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
                        class="rounded-full border border-border px-3 py-1 text-foreground transition-colors hover:border-[var(--orange)] hover:text-[var(--orange)]"
                    >
                        #{{ tag.name }}
                    </Link>
                </div>

                <!-- Back link -->
                <div class="mt-10">
                    <Link
                        :href="localizedUrl(locale, '/blog')"
                        class="mv-blogpost-back group inline-flex items-center gap-2 text-sm font-medium text-[var(--orange)]"
                    >
                        <ArrowLeft
                            class="size-4 transition-transform duration-300 group-hover:-translate-x-1"
                        />
                        {{ t.back }}
                    </Link>
                </div>
            </article>
        </div>
    </div>

    <!-- Related posts -->
    <section
        v-if="related.length > 0"
        class="mv-blog-index mv-blogpost-related border-t border-border/60"
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
