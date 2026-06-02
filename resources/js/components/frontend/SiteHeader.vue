<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { getDefaultLocale, localizedUrl } from '@/lib/localizedUrl';
import HeaderMenuNode from './HeaderMenuNode.vue';
import HeaderMobileMenuNode from './HeaderMobileMenuNode.vue';
import type { MenuNode } from './menu-types';

const props = defineProps<{
    locale: string;
}>();

const page = usePage();
const open = ref(false);
const openDropdownId = ref<number | null>(null);

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

                <div class="flex items-center gap-4">
                    <Link
                        :href="switchHref"
                        class="mv-lang-switch hidden sm:inline-flex"
                    >
                        {{ otherLocaleLabel }}
                    </Link>
                    <a
                        href="#angebot"
                        class="mv-header-cta hidden lg:inline-flex"
                    >
                        {{ t.cta }} <ArrowRight :size="14" />
                    </a>
                    <button
                        type="button"
                        class="mv-header-toggle inline-flex lg:hidden"
                        :aria-expanded="open"
                        aria-label="Open menu"
                        @click="open = true"
                    >
                        <Menu :size="20" />
                    </button>
                </div>
            </div>
        </nav>

        <!-- ===== FULL-SCREEN OFFCANVAS (teleported to end of <body>) ===== -->
        <Teleport to="body">
            <div
                class="mv-offcanvas"
                :class="{ 'is-open': open }"
                role="dialog"
                aria-modal="true"
                :aria-hidden="!open"
                :aria-label="t.menu"
            >
                <button
                    type="button"
                    class="mv-offcanvas__close"
                    aria-label="Close menu"
                    @click="open = false"
                >
                    <X :size="24" />
                </button>

            <div class="mv-offcanvas__inner">
                <div class="mv-offcanvas__body">
                    <!-- Contact -->
                    <div class="mv-offcanvas__contact">
                        <div class="mv-offcanvas__contact-row">
                            <div>
                                <p class="mv-offcanvas__label">
                                    {{ t.sendMessage }}
                                </p>
                                <a
                                    v-for="email in emails"
                                    :key="email"
                                    :href="`mailto:${email}`"
                                    class="mv-offcanvas__muted-link"
                                >
                                    {{ email }}
                                </a>
                            </div>
                            <div>
                                <p class="mv-offcanvas__label">
                                    {{ t.callUs }}
                                </p>
                                <a
                                    v-for="phone in phones"
                                    :key="phone.tel"
                                    :href="`tel:${phone.tel}`"
                                    class="mv-offcanvas__muted-link"
                                >
                                    {{ phone.display }}
                                </a>
                                <span class="mv-offcanvas__muted-link">
                                    {{ t.openingHours }}: {{ t.hours }}
                                </span>
                            </div>
                        </div>
                        <div class="mv-offcanvas__visit">
                            <p class="mv-offcanvas__label">{{ t.visitUs }}</p>
                            <p class="mv-offcanvas__muted">{{ address }}</p>
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
