<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    locale: string;
}>();

const page = usePage();
const open = ref(false);

const otherLocale = computed(() => (props.locale === 'de' ? 'en' : 'de'));
const otherLocaleLabel = computed(() => (props.locale === 'de' ? 'EN' : 'DE'));

// Build the equivalent URL in the other locale by swapping the first segment.
const switchHref = computed(() => {
    const path = (page.url ?? `/${props.locale}`).split('?')[0];
    const swapped = path.replace(
        /^\/(de|en)(\/|$)/,
        `/${otherLocale.value}$2`,
    );
    return swapped;
});

const navItems = computed(() => [
    { label: props.locale === 'de' ? 'Startseite' : 'Home', href: `/${props.locale}` },
    { label: props.locale === 'de' ? 'Blog' : 'Blog', href: `/${props.locale}/blog` },
    { label: props.locale === 'de' ? 'Seiten' : 'Pages', href: `/${props.locale}/pages` },
]);
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b border-border/60 bg-background/80 backdrop-blur"
    >
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4">
            <Link
                :href="`/${locale}`"
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
