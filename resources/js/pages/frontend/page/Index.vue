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

type WidgetVisibility = {
    desktop: boolean;
    tablet: boolean;
    mobile: boolean;
};

type WidgetPayload = {
    type: string;
    settings: Record<string, unknown>;
    data: Record<string, unknown>;
    visibility?: WidgetVisibility;
    css_class?: string;
};

const props = defineProps<{
    locale: string;
    page: Page;
    widgets?: WidgetPayload[];
}>();

// Map per-breakpoint visibility booleans into Tailwind utilities. Tailwind's
// breakpoints we map onto: mobile = base (< 768px), tablet = md (768-1023px),
// desktop = lg+ (>= 1024px). We only emit overrides when adjacent breakpoints
// differ, so "show on all" produces no class at all.
function visibilityClass(v?: WidgetVisibility): string {
    if (!v) {
        return '';
    }

    const { mobile, tablet, desktop } = v;

    if (mobile && tablet && desktop) {
        return '';
    }

    const parts: string[] = [];
    parts.push(mobile ? 'block' : 'hidden');

    if (tablet !== mobile) {
        parts.push(tablet ? 'md:block' : 'md:hidden');
    }

    if (desktop !== tablet) {
        parts.push(desktop ? 'lg:block' : 'lg:hidden');
    }

    return parts.join(' ');
}

const widgetStack = computed(() =>
    (props.widgets ?? [])
        .map((w) => ({
            widget: w,
            entry: getWidgetEntry(w.type),
            wrapperClass: [visibilityClass(w.visibility), w.css_class ?? '']
                .filter(Boolean)
                .join(' '),
        }))
        .filter(
            (
                row,
            ): row is {
                widget: WidgetPayload;
                entry: NonNullable<ReturnType<typeof getWidgetEntry>>;
                wrapperClass: string;
            } => row.entry !== null,
        ),
);

// app.ts skips FrontendLayout for this page — we own the chrome decision
// here so the "nolayout" template can render bare (no SiteHeader / SiteFooter).
const t = computed(() => ({
    home: props.locale === 'de' ? 'Startseite' : 'Home',
}));

const isFullwidth = computed(() => props.page.template === 'fullwidth');
const isNolayout = computed(() => props.page.template === 'nolayout');
// "default" is the implicit fallback.

// Banner widgets render their own breadcrumb, so suppress the layout-level
// breadcrumb when one is present to avoid showing it twice.
const hasOwnBreadcrumb = computed(() =>
    (props.widgets ?? []).some((w) => w.type === 'orbit_banner'),
);
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
    <div v-if="isNolayout" class="min-h-screen bg-background text-foreground">
        <component
            :is="row.entry.renderer"
            v-for="(row, i) in widgetStack"
            :key="i"
            :class="row.wrapperClass || undefined"
            :settings="row.widget.settings"
            :data="row.widget.data"
        />
    </div>

    <!-- DEFAULT or FULL WIDTH: wrap in FrontendLayout (header + footer) -->
    <FrontendLayout v-else>
        <!-- Breadcrumb: shown on every page EXCEPT the home page, for both
             the default and fullwidth templates. Fullwidth gets its own thin
             breadcrumb bar since it skips the prose article below. -->
        <div
            v-if="isFullwidth && !page.is_home && !hasOwnBreadcrumb"
            class="container-xl pt-8"
        >
            <nav
                class="mv-pagebanner-crumbs mv-crumbs-onlight"
                aria-label="Breadcrumb"
            >
                <Link
                    :href="localizedUrl(locale, '/')"
                    class="hover:text-foreground"
                >
                    {{ t.home }}
                </Link>
                <span class="sep">›</span>
                <span class="is-current line-clamp-1">{{ page.title }}</span>
            </nav>
        </div>

        <!-- DEFAULT only: centered prose with breadcrumb + page title +
             hero image. Fullwidth skips this so widgets sit edge-to-edge
             with no breadcrumb chrome on top. -->
        <article v-if="false" class="container-xl py-10">
            <nav
                v-if="!page.is_home && !hasOwnBreadcrumb"
                class="mv-pagebanner-crumbs mv-crumbs-onlight mb-6"
                aria-label="Breadcrumb"
            >
                <Link
                    :href="localizedUrl(locale, '/')"
                    class="hover:text-foreground"
                >
                    {{ t.home }}
                </Link>
                <span class="sep">›</span>
                <span class="is-current line-clamp-1">
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

        <!-- Widgets render once for BOTH templates (default + fullwidth). -->
        <component
            :is="row.entry.renderer"
            v-for="(row, i) in widgetStack"
            :key="i"
            :class="row.wrapperClass || undefined"
            :settings="row.widget.settings"
            :data="row.widget.data"
        />
    </FrontendLayout>
</template>
