<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown, Menu, X } from 'lucide-vue-next';
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

const switchHref = computed(() => {
    const url = page.url ?? '/';
    const [pathOnly, query = ''] = url.split('?');
    const withoutPrefix = pathOnly.replace(/^\/(de|en)(?=\/|$)/, '') || '/';
    const target = localizedUrl(otherLocale.value, withoutPrefix);
    return query !== '' ? `${target}?${query}` : target;
});

const home = computed(() => localizedUrl(props.locale, '/'));

// Header menu comes from HandleInertiaRequests::share() — resolved per
// locale on the server and cached for 1 hour, busted on menu edits.
const headerMenu = computed<MenuNode[]>(() => {
    const m = (page.props as Record<string, unknown>).headerMenu;
    return Array.isArray(m) ? (m as MenuNode[]) : [];
});

type SiteSettingsLite = {
    site_name?: string | null;
    logo_light_url?: string | null;
    logo_dark_url?: string | null;
};
const siteSettings = computed<SiteSettingsLite>(
    () => ((page.props as Record<string, unknown>).siteSettings ?? {}) as SiteSettingsLite,
);
const siteName = computed<string>(() => siteSettings.value.site_name || 'Moovato');
const logoUrl = computed<string | null>(() => siteSettings.value.logo_light_url ?? null);
const brandInitial = computed<string>(() => siteName.value.charAt(0).toUpperCase());

type SiteLayout = {
    header_sticky?: boolean;
    show_language_switcher?: boolean;
};
const layout = computed<SiteLayout>(
    () => ((page.props as Record<string, unknown>).siteLayout ?? {}) as SiteLayout,
);
const headerSticky = computed<boolean>(() => layout.value.header_sticky !== false);
const showLanguageSwitcher = computed<boolean>(
    () => layout.value.show_language_switcher !== false,
);

const sharedDefault = (page.props as Record<string, unknown>).defaultLocale;
if (typeof sharedDefault === 'string' && sharedDefault !== getDefaultLocale()) {
    import('@/lib/localizedUrl').then((m) => m.setDefaultLocale(sharedDefault));
}
</script>

<template>
    <header
        class="z-40 border-b border-border/60 bg-background/80 backdrop-blur"
        :class="headerSticky ? 'sticky top-0' : ''"
    >
        <div
            class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4"
        >
            <Link
                :href="home"
                class="flex items-center gap-2 text-lg font-semibold tracking-tight"
            >
                <img
                    v-if="logoUrl"
                    :src="logoUrl"
                    alt="Moovato logo"
                    class="h-8 w-auto object-contain"
                />
                <span
                    v-else
                    class="inline-flex size-8 items-center justify-center rounded-md bg-primary font-bold text-primary-foreground"
                >{{ brandInitial }}</span>
                <span>{{ siteName }}</span>
            </Link>

            <nav class="hidden items-center gap-6 text-base md:flex">
                <template v-for="item in headerMenu" :key="item.id">
                    <!-- Has children → dropdown trigger -->
                    <div
                        v-if="item.children.length > 0"
                        class="relative"
                        @mouseenter="openDropdownId = item.id"
                        @mouseleave="openDropdownId = null"
                    >
                        <a
                            v-if="item.url && item.open_in_new_tab"
                            :href="item.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 text-muted-foreground transition-colors hover:text-foreground"
                            :class="item.css_class ?? ''"
                        >
                            {{ item.label }}
                            <ChevronDown class="size-3.5" />
                        </a>
                        <Link
                            v-else-if="item.url"
                            :href="item.url"
                            class="inline-flex items-center gap-1 text-muted-foreground transition-colors hover:text-foreground"
                            :class="item.css_class ?? ''"
                        >
                            {{ item.label }}
                            <ChevronDown class="size-3.5" />
                        </Link>
                        <button
                            v-else
                            type="button"
                            class="inline-flex items-center gap-1 text-muted-foreground transition-colors hover:text-foreground"
                            :class="item.css_class ?? ''"
                        >
                            {{ item.label }}
                            <ChevronDown class="size-3.5" />
                        </button>
                        <!-- First-level panel — descendants render recursively
                             so the tree can nest as deep as the admin built it. -->
                        <div
                            v-if="openDropdownId === item.id"
                            class="absolute left-0 top-full z-40 min-w-[200px] rounded-md border border-border/60 bg-background p-1 shadow-lg"
                        >
                            <HeaderMenuNode
                                v-for="child in item.children"
                                :key="child.id"
                                :node="child"
                            />
                        </div>
                    </div>

                    <!-- Leaf with URL — new-tab uses plain <a>; internal nav uses <Link>. -->
                    <a
                        v-else-if="item.url && item.open_in_new_tab"
                        :href="item.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-muted-foreground transition-colors hover:text-foreground"
                        :class="item.css_class ?? ''"
                    >
                        {{ item.label }}
                    </a>
                    <Link
                        v-else-if="item.url"
                        :href="item.url"
                        class="text-muted-foreground transition-colors hover:text-foreground"
                        :class="item.css_class ?? ''"
                    >
                        {{ item.label }}
                    </Link>

                    <!-- Leaf without URL (category) — render label-only so the
                         admin's intent is still visible in the nav. -->
                    <span
                        v-else
                        class="text-muted-foreground"
                        :class="item.css_class ?? ''"
                    >{{ item.label }}</span>
                </template>
            </nav>

            <div class="flex items-center gap-2">
                <Link
                    v-if="showLanguageSwitcher"
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

        <!-- Mobile drawer: recursive, indented by depth (no cascading panels). -->
        <div
            v-if="open"
            class="border-t border-border/60 bg-background md:hidden"
        >
            <nav class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-3 text-sm">
                <HeaderMobileMenuNode
                    v-for="item in headerMenu"
                    :key="item.id"
                    :node="item"
                    @navigate="open = false"
                />
                <Link
                    v-if="showLanguageSwitcher"
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
