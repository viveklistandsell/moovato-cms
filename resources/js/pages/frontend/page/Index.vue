<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import FrontendLayout from '@/layouts/frontend/FrontendLayout.vue';
import { localizedUrl } from '@/lib/localizedUrl';
import { getWidgetEntry } from '@/widgets/registry';

type Category = { id: number; title: string; permalink: string };

type Page = {
    id: number;
    title: string;
    permalink: string;
    image_url: string | null;
    template: string;
    is_home: boolean;
    author: string | null;
    created_at: string | null;
    categories: Category[];
};

type WidgetPayload = {
    type: string;
    settings: Record<string, unknown>;
    data: Record<string, unknown>;
};

const props = defineProps<{
    locale: string;
    page: Page;
    widgets?: WidgetPayload[];
}>();

const widgetStack = computed(() =>
    (props.widgets ?? [])
        .map((w) => ({ widget: w, entry: getWidgetEntry(w.type) }))
        .filter((row): row is { widget: WidgetPayload; entry: NonNullable<ReturnType<typeof getWidgetEntry>> } => row.entry !== null),
);

// app.ts skips FrontendLayout for this page — we own the chrome decision
// here so the "nolayout" template can render bare (no SiteHeader / SiteFooter).
const t = computed(() => ({
    home: props.locale === 'de' ? 'Startseite' : 'Home',
}));

const isFullwidth = computed(() => props.page.template === 'fullwidth');
const isNolayout = computed(() => props.page.template === 'nolayout');
// "default" is the implicit fallback.
</script>

<template>
    <Head>
        <title>{{ page.title }}</title>
        <meta head-key="og:title" property="og:title" :content="page.title" />
        <meta
            v-if="page.image_url"
            head-key="og:image"
            property="og:image"
            :content="page.image_url"
        />
    </Head>

    <!-- NO LAYOUT: bare widgets, no header/footer/breadcrumb -->
    <div
        v-if="isNolayout"
        class="min-h-screen bg-background text-foreground"
    >
        <component
            :is="row.entry.renderer"
            v-for="(row, i) in widgetStack"
            :key="i"
            :settings="row.widget.settings"
            :data="row.widget.data"
        />
    </div>

    <!-- DEFAULT or FULL WIDTH: wrap in FrontendLayout (header + footer) -->
    <FrontendLayout v-else>
        <!-- FULL WIDTH: come with proper header and footer, edge-to-edge content, wider reading column -->
        <article v-if="isFullwidth" class="w-full">
            <component
                :is="row.entry.renderer"
                v-for="(row, i) in widgetStack"
                :key="i"
                :settings="row.widget.settings"
                :data="row.widget.data"
            />
        </article>

        <!-- DEFAULT: centered prose with breadcrumb and hero image -->
        <article v-else class="mx-auto max-w-3xl px-4 py-10">
            <nav
                class="mb-6 flex items-center gap-2 text-sm text-muted-foreground"
            >
                <Link
                    :href="localizedUrl(locale, '/')"
                    class="hover:text-foreground"
                >
                    {{ t.home }}
                </Link>
                <span>›</span>
                <span class="line-clamp-1 text-foreground">
                    {{ page.title }}
                </span>
            </nav>

            <h1
                class="mb-6 text-3xl font-extrabold tracking-tight sm:text-4xl md:text-5xl"
            >
                {{ page.title }}
            </h1>

            <div
                v-if="page.image_url"
                class="mb-8 overflow-hidden rounded-xl bg-muted"
            >
                <img
                    :src="page.image_url"
                    :alt="page.title"
                    class="aspect-[16/9] w-full object-cover"
                />
            </div>
        </article>

        <component
            :is="row.entry.renderer"
            v-for="(row, i) in widgetStack"
            :key="i"
            :settings="row.widget.settings"
            :data="row.widget.data"
        />
    </FrontendLayout>
</template>
