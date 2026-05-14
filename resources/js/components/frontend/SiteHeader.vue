<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { getDefaultLocale, localizedUrl } from '@/lib/localizedUrl';

const props = defineProps<{
    locale: string;
}>();

const page = usePage();
const open = ref(false);

const otherLocale = computed(() => (props.locale === 'de' ? 'en' : 'de'));
const otherLocaleLabel = computed(() => (props.locale === 'de' ? 'EN' : 'DE'));

// Build the equivalent URL in the other locale.
// Strip ANY existing locale prefix (/de, /en) — including legacy /de paths —
// then re-add the locale segment only when the target is not the default locale.
const switchHref = computed(() => {
    const url = page.url ?? '/';
    const [pathOnly, query = ''] = url.split('?');
    const withoutPrefix = pathOnly.replace(/^\/(de|en)(?=\/|$)/, '') || '/';
    const target = localizedUrl(otherLocale.value, withoutPrefix);
    return query !== '' ? `${target}?${query}` : target;
});

const home = computed(() => localizedUrl(props.locale, '/'));
const navItems = computed(() => [
    {
        label: props.locale === 'de' ? 'Startseite' : 'Home',
        href: home.value,
    },
    {
        label: 'Blog',
        href: localizedUrl(props.locale, '/blog'),
    },
    {
        label: props.locale === 'de' ? 'Seiten' : 'Pages',
        href: localizedUrl(props.locale, '/pages'),
    },
]);

// Keep the cached default locale in sync with whatever the server shares,
// in case the project ever changes the default away from "de".
const sharedDefault = (page.props as Record<string, unknown>).defaultLocale;
if (typeof sharedDefault === 'string' && sharedDefault !== getDefaultLocale()) {
    // Lazy import to avoid a top-level side effect during SSR setup.
    import('@/lib/localizedUrl').then((m) => m.setDefaultLocale(sharedDefault));
}
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b border-border/60 bg-background/80 backdrop-blur"
    >
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4">
            <Link
                :href="home"
                class="flex items-center gap-2 text-lg font-semibold tracking-tight"
            >
                <span class="inline-flex size-8 items-center justify-center rounded-md bg-primary text-primary-foreground font-bold">M</span>
                <span>Moovato</span>
            </Link>

            <nav class="hidden items-center gap-6 text-lg md:flex">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="text-muted-foreground transition-colors hover:text-foreground"
                >
                    {{ item.label }}
                </Link>
            </nav>

            <div class="flex items-center gap-2">
                <Link
                    :href="switchHref"
                    class="hidden rounded-md border border-border px-2.5 py-1 text-xs font-medium uppercase tracking-wide text-muted-foreground hover:border-foreground hover:text-foreground sm:inline-flex"
                >
                    {{ otherLocaleLabel }}
                </Link>
                <button
                    type="button"
                    class="inline-flex size-9 items-center justify-center rounded-md border border-border md:hidden"
                    @click="open = !open"
                >
                    <component :is="open ? X : Menu" class="size-4" />
                </button>
            </div>
        </div>

        <div
            v-if="open"
            class="border-t border-border/60 bg-background md:hidden"
        >
            <nav class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-3 text-sm">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="rounded-md px-2 py-2 hover:bg-muted"
                    @click="open = false"
                >
                    {{ item.label }}
                </Link>
                <Link
                    :href="switchHref"
                    class="rounded-md px-2 py-2 text-xs uppercase tracking-wide text-muted-foreground hover:bg-muted"
                    @click="open = false"
                >
                    Switch to {{ otherLocaleLabel }}
                </Link>
            </nav>
        </div>
    </header>
</template>
