<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    ChevronDown,
    ClipboardList,
    Clock,
    Facebook,
    Home,
    Instagram,
    Linkedin,
    Mail,
    Menu,
    Phone,
    Truck,
    Twitter,
    X,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import FlagImage from '@/components/common/FlagImage.vue';
import { getDefaultLocale, localizedUrl, setDefaultLocale } from '@/lib/localizedUrl';

const props = defineProps<{
    locale: string;
}>();

const page = usePage();
const open = ref(false);

const otherLocale = computed(() => (props.locale === 'de' ? 'en' : 'de'));
type SharedLanguage = { code: string; native_name: string; flag: string | null };
const sharedLanguages = computed<SharedLanguage[]>(
    () => (page.props.adminLanguages as SharedLanguage[] | undefined) ?? [],
);
const currentLangFlag = computed<string | null>(
    () => sharedLanguages.value.find((l) => l.code === props.locale)?.flag ?? null,
);

function hrefForLocale(target: string): string {
    const alternates = (page.props.localeAlternates as Record<string, string> | undefined) ?? null;
    const supplied = alternates?.[target];
    if (typeof supplied === 'string' && supplied.length > 0) {
        const url = page.url ?? '/';
        const [, query = ''] = url.split('?');

        return query !== '' ? `${supplied}?${query}` : supplied;
    }

    const url = page.url ?? '/';
    const [pathOnly, query = ''] = url.split('?');
    const withoutPrefix = pathOnly.replace(/^\/(de|en)(?=\/|$)/, '') || '/';
    const fallback = localizedUrl(target, withoutPrefix);

    return query !== '' ? `${fallback}?${query}` : fallback;
}


/* -------------------- language dropdown -------------------- */

type LocaleChoice = { code: string; short: string; label: string; flag: string | null };
const availableLocales = computed<LocaleChoice[]>(() => {
    const shared = sharedLanguages.value;
    if (shared.length > 0) {
        return shared.map((l) => ({
            code: l.code,
            short: l.code.toUpperCase(),
            label: l.native_name,
            flag: l.flag,
        }));
    }

    return [
        { code: 'de', short: 'DE', label: 'Deutsch', flag: null },
        { code: 'en', short: 'EN', label: 'English', flag: null },
    ];
});
const currentLocaleMeta = computed<LocaleChoice | undefined>(
    () => availableLocales.value.find((l) => l.code === props.locale),
);

const langMenuOpen = ref(false);
const langMenuRef = ref<HTMLDivElement | null>(null);
const langMenuMobileRef = ref<HTMLDivElement | null>(null);

function toggleLangMenu(): void {
    langMenuOpen.value = !langMenuOpen.value;
}
function onLocaleClick(): void {
    langMenuOpen.value = false;
}

function onDocClick(e: MouseEvent): void {
    if (! langMenuOpen.value) return;
    const target = e.target as Node;
    const inDesktop = langMenuRef.value?.contains(target) ?? false;
    const inMobile = langMenuMobileRef.value?.contains(target) ?? false;
    if (! inDesktop && ! inMobile) {
        langMenuOpen.value = false;
    }
}
onMounted(() => document.addEventListener('click', onDocClick));
onBeforeUnmount(() => document.removeEventListener('click', onDocClick));

const home = computed(() => localizedUrl(props.locale, '/'));

type HeaderMenuItem = {
    id: number;
    type: string;
    label: string;
    url: string | null;
    open_in_new_tab: boolean;
    css_class: string | null;
    children: HeaderMenuItem[];
};

type NavLink = {
    label: string;
    href: string;
    newTab: boolean;
    cssClass: string | null;
    children: NavLink[];
};

const headerMenu = computed<HeaderMenuItem[]>(
    () => (page.props.headerMenu as HeaderMenuItem[] | undefined) ?? [],
);

function toNavLink(item: HeaderMenuItem): NavLink {
    return {
        label: item.label,
        href: item.url ?? '#',
        newTab: item.open_in_new_tab,
        cssClass: item.css_class,
        children: (item.children ?? []).map(toNavLink),
    };
}

const fallbackNav = computed<NavLink[]>(() => [
    {
        label: props.locale === 'de' ? 'Startseite' : 'Home',
        href: home.value,
        newTab: false,
        cssClass: null,
        children: [],
    },
    {
        label: 'Blog',
        href: localizedUrl(props.locale, '/blog'),
        newTab: false,
        cssClass: null,
        children: [],
    },
    {
        label: props.locale === 'de' ? 'Seiten' : 'Pages',
        href: localizedUrl(props.locale, '/pages'),
        newTab: false,
        cssClass: null,
        children: [],
    },
]);

const navItems = computed<NavLink[]>(() =>
    headerMenu.value.length > 0
        ? headerMenu.value.map(toNavLink)
        : fallbackNav.value,
);

function isSpaLink(href: string): boolean {
    return href.startsWith('/') && !href.startsWith('//');
}

const t = computed(() =>
    props.locale === 'de'
        ? {
              emailLabel: 'E-Mail',
              callLabel: 'Anrufen',
              hours: 'Mo–Fr 8–18 Uhr',
              cta: 'Angebot anfordern',
              sendMessage: 'Nachricht senden',
              callUs: 'Anrufen',
              openingHours: 'Öffnungszeiten',
              visitUs: 'Besuchen Sie uns',
              letsTalk: 'Kontakt aufnehmen',
              home: 'Startseite',
              services: 'Leistungen',
              menu: 'Menü',
          }
        : {
              emailLabel: 'Email',
              callLabel: 'Call',
              hours: 'Mon–Fri 8am–6pm',
              cta: 'Get a quote',
              sendMessage: 'Send a message',
              callUs: 'Call us',
              openingHours: 'Opening hours',
              visitUs: 'Visit Us',
              letsTalk: "Let's talk",
              home: 'Home',
              services: 'Services',
              menu: 'Menu',
          },
);

type SiteSettings = {
    site_name?: string | null;
    logo_light_url?: string | null;
    logo_dark_url?: string | null;
    phone?: string | null;
    email?: string | null;
    address?: string | null;
    whatsapp?: string | null;
    facebook_url?: string | null;
    twitter_url?: string | null;
    linkedin_url?: string | null;
    instagram_url?: string | null;
};
const siteSettings = computed<SiteSettings>(
    () => (page.props.siteSettings as SiteSettings | undefined) ?? {},
);

// Layout toggles from Settings → Layout Toggles. Default `true` so an
// upgrade that doesn't ship this prop yet keeps the historic behaviour.
type SiteLayout = {
    show_language_switcher?: boolean;
    header_sticky?: boolean;
};
const siteLayout = computed<SiteLayout>(
    () => (page.props.siteLayout as SiteLayout | undefined) ?? {},
);
const showLanguageSwitcher = computed<boolean>(
    () => siteLayout.value.show_language_switcher !== false,
);

const headerSticky = computed<boolean>(
    () => siteLayout.value.header_sticky !== false,
);

const siteName = computed<string>(
    () => siteSettings.value.site_name || 'Moovato',
);
const logoUrl = computed<string | null>(
    () => siteSettings.value.logo_light_url ?? null,
);

const emails = computed<string[]>(() => {
    const e = siteSettings.value.email;
    return e ? [e] : [];
});
const phones = computed<Array<{ display: string; tel: string }>>(() => {
    const p = siteSettings.value.phone;
    if (!p) return [];
    return [{ display: p, tel: p.replace(/[^\d+]/g, '') }];
});
const address = computed<string>(() => siteSettings.value.address || '');

// Social icon row — only show entries the admin has actually filled in.
const socials = computed<
    Array<{ label: string; icon: typeof Facebook; href: string }>
>(() => {
    const s = siteSettings.value;
    const out: Array<{ label: string; icon: typeof Facebook; href: string }> =
        [];
    if (s.facebook_url)
        out.push({ label: 'Facebook', icon: Facebook, href: s.facebook_url });
    if (s.instagram_url)
        out.push({
            label: 'Instagram',
            icon: Instagram,
            href: s.instagram_url,
        });
    if (s.twitter_url)
        out.push({ label: 'Twitter', icon: Twitter, href: s.twitter_url });
    if (s.linkedin_url)
        out.push({ label: 'LinkedIn', icon: Linkedin, href: s.linkedin_url });
    return out;
});

const socialLinks = computed<
    Array<{ abbr: string; label: string; href: string }>
>(() => {
    const s = siteSettings.value;
    const out: Array<{ abbr: string; label: string; href: string }> = [];
    if (s.facebook_url)
        out.push({ abbr: 'FB', label: 'Facebook', href: s.facebook_url });
    if (s.twitter_url)
        out.push({ abbr: 'X', label: 'Twitter', href: s.twitter_url });
    if (s.instagram_url)
        out.push({ abbr: 'IN', label: 'Instagram', href: s.instagram_url });
    if (s.linkedin_url)
        out.push({ abbr: 'LN', label: 'LinkedIn', href: s.linkedin_url });
    return out;
});

watch(open, (isOpen) => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = isOpen ? 'hidden' : '';
    }
});

function onKeydown(e: KeyboardEvent): void {
    if (e.key === 'Escape') {
        open.value = false;
    }
}

// Reveal-on-scroll sticky: the header stays in normal flow at the top and
// only pins itself once the user has scrolled past its own height, sliding
// back into view. A spacer keeps the layout from jumping when it detaches.
const headerEl = ref<HTMLElement | null>(null);
const navEl = ref<HTMLElement | null>(null);
const headerHeight = ref(0);
const navHeight = ref(0);
const isStuck = ref(false);

function onScroll(): void {
    if (!headerSticky.value) {
        isStuck.value = false;
        return;
    }
    const threshold = headerHeight.value || 200;
    isStuck.value = window.scrollY > threshold;
}

onMounted(() => {
    window.addEventListener('keydown', onKeydown);
    headerHeight.value = headerEl.value?.offsetHeight ?? 0;
    navHeight.value = navEl.value?.offsetHeight ?? 0;
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});
onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);
    window.removeEventListener('scroll', onScroll);
    if (typeof document !== 'undefined') {
        document.body.style.overflow = '';
    }
});

// Keep the cached default locale in sync with whatever the server shares.
const sharedDefault = (page.props as Record<string, unknown>).defaultLocale;
if (typeof sharedDefault === 'string' && sharedDefault !== getDefaultLocale()) {
    setDefaultLocale(sharedDefault);
}
</script>

<template>
    <div>
    <header
        ref="headerEl"
        class="mv-header"
        
    >
        <!-- ===== TOP BAR ===== -->
        <div class="topbar">
            <div
                class="topbar-inner relative z-[1] container-xl flex flex-wrap items-center justify-between gap-6"
            >
                <div class="topbar-welcome inline-flex items-center gap-1">
                    <span class="ico"><Clock :size="15" /></span>
                    <strong>{{ t.openingHours }}:</strong> {{ t.hours }}
                    <div class="topbar-socials">
                        <a
                            v-for="s in socials"
                            :key="s.label"
                            :href="s.href"
                            :aria-label="s.label"
                        >
                            <component :is="s.icon" :size="15" />
                        </a>
                    </div>
                </div>
                <div class="topbar-contacts">
                    <div v-for="email in emails" :key="email" class="item">
                        <span class="ico"><Mail :size="15" /></span>
                        {{ t.emailLabel }}:
                        <a :href="`mailto:${email}`">{{ email }}</a>
                    </div>
                    <div v-for="phone in phones" :key="phone.tel" class="item">
                        <span class="ico"><Phone :size="15" /></span>
                        {{ t.callLabel }}:
                        <a :href="`tel:${phone.tel}`">{{ phone.display }}</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== NAVIGATION (sticky) ===== -->
        <nav
            ref="navEl"
            class="mv-header-nav border-b border-[rgba(15,23,42,0.06)] bg-white py-[10px]"
            :class="{ 'is-stuck': isStuck }"
        >
            <div class="container-xl flex items-center justify-between gap-8">
                <Link :href="home" class="flex items-center no-underline">
                    <img
                        :src="logoUrl ?? '/logo.svg'"
                        :alt="siteName"
                        class="h-15 w-auto"
                    />
                </Link>

                <ul
                    class="hidden list-none gap-8 lg:flex lg:flex-1 lg:justify-center"
                >
                    <li
                        v-for="item in navItems"
                        :key="item.label"
                        class="mv-nav-item"
                        :class="item.cssClass"
                    >
                        <component
                            :is="isSpaLink(item.href) ? Link : 'a'"
                            :href="item.href"
                            :target="item.newTab ? '_blank' : undefined"
                            :rel="
                                item.newTab ? 'noopener noreferrer' : undefined
                            "
                            class="mv-nav-link"
                        >
                            {{ item.label }}
                            <ChevronDown
                                v-if="item.children.length"
                                :size="14"
                            />
                        </component>
                        <ul v-if="item.children.length" class="mv-nav-sub">
                            <li
                                v-for="child in item.children"
                                :key="child.label"
                            >
                                <component
                                    :is="isSpaLink(child.href) ? Link : 'a'"
                                    :href="child.href"
                                    :target="
                                        child.newTab ? '_blank' : undefined
                                    "
                                    :rel="
                                        child.newTab
                                            ? 'noopener noreferrer'
                                            : undefined
                                    "
                                    class="mv-nav-sublink"
                                >
                                    {{ child.label }}
                                </component>
                            </li>
                        </ul>
                    </li>
                </ul>

                <div class="flex items-center gap-4">
                    <div
                        v-if="showLanguageSwitcher"
                        ref="langMenuRef"
                        class="mv-lang-dropdown hidden sm:inline-block"
                    >
                        <button
                            type="button"
                            class="mv-lang-switch"
                            :aria-expanded="langMenuOpen"
                            aria-haspopup="menu"
                            @click.stop="toggleLangMenu"
                        >
                            <FlagImage :code="currentLangFlag" size="sm" />
                            <span>{{ (currentLocaleMeta?.short) ?? props.locale.toUpperCase() }}</span>
                            <ChevronDown
                                :size="14"
                                class="mv-lang-caret"
                                :class="langMenuOpen ? 'is-open' : ''"
                            />
                        </button>
                        <ul
                            v-if="langMenuOpen"
                            role="menu"
                            class="mv-lang-menu"
                        >
                            <li v-for="l in availableLocales" :key="l.code" role="none">
                                <Link
                                    role="menuitem"
                                    :href="hrefForLocale(l.code)"
                                    class="mv-lang-menu__item"
                                    :class="l.code === props.locale ? 'is-current' : ''"
                                    @click="onLocaleClick"
                                >
                                    <FlagImage :code="l.flag" size="sm" />
                                    <span class="mv-lang-menu__label">{{ l.label }}</span>
                                    <span class="mv-lang-menu__short">{{ l.short }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <a
                        href="#angebot"
                        class="mv-header-cta hidden lg:inline-flex"
                    >
                        {{ t.cta }} <ArrowRight :size="14" />
                    </a>
                    <button
                        type="button"
                        class="mv-header-toggle hidden md:inline-flex lg:hidden"
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
                                <p class="mv-offcanvas__label">
                                    {{ t.visitUs }}
                                </p>
                                <p class="mv-offcanvas__muted">{{ address }}</p>
                            </div>
                        </div>

                        <!-- Big nav -->
                        <nav class="mv-offcanvas__nav">
                            <template
                                v-for="item in navItems"
                                :key="item.label"
                            >
                                <component
                                    :is="isSpaLink(item.href) ? Link : 'a'"
                                    :href="item.href"
                                    :target="item.newTab ? '_blank' : undefined"
                                    :rel="
                                        item.newTab
                                            ? 'noopener noreferrer'
                                            : undefined
                                    "
                                    @click="open = false"
                                >
                                    {{ item.label }}
                                </component>
                                <component
                                    :is="isSpaLink(child.href) ? Link : 'a'"
                                    v-for="child in item.children"
                                    :key="child.label"
                                    :href="child.href"
                                    :target="
                                        child.newTab ? '_blank' : undefined
                                    "
                                    :rel="
                                        child.newTab
                                            ? 'noopener noreferrer'
                                            : undefined
                                    "
                                    class="mv-offcanvas__subnav"
                                    @click="open = false"
                                >
                                    {{ child.label }}
                                </component>
                            </template>
                            <a href="#angebot" @click="open = false">{{
                                t.letsTalk
                            }}</a>
                        </nav>
                    </div>

                <!-- Footer row: language switch + socials -->
                <div class="mv-offcanvas__foot">
                    <div
                        v-if="showLanguageSwitcher"
                        ref="langMenuMobileRef"
                        class="mv-offcanvas__langs"
                    >
                        <Link
                            v-for="l in availableLocales"
                            :key="l.code"
                            :href="hrefForLocale(l.code)"
                            class="mv-offcanvas__lang"
                            :class="l.code === props.locale ? 'is-current' : ''"
                            @click="() => { onLocaleClick(); open = false; }"
                        >
                            <FlagImage :code="l.flag" size="sm" />
                            <span>{{ l.short }}</span>
                        </Link>
                    </div>
                    <div class="mv-offcanvas__socials">
                        <a
                            v-for="s in socialLinks"
                            :key="s.abbr"
                            :href="s.href"
                            :aria-label="s.label"
                        >
                            {{ s.abbr }}
                        </a>
                    </div>
                </div>
            </div>
            </div>
        </Teleport>

        <nav
            class="fixed inset-x-0 bottom-0 z-50 flex items-end justify-around border-t border-[rgba(15,23,42,0.08)] bg-white px-2 pt-2 pb-[max(env(safe-area-inset-bottom),0.5rem)] shadow-[0_-6px_24px_rgba(15,23,42,0.08)] md:hidden"
            aria-label="Mobile"
        >
            <Link
                :href="home"
                class="flex flex-1 flex-col items-center gap-1 py-1 text-[11px] font-medium text-[color:var(--midnight)] transition-colors hover:text-[color:var(--orange)]"
            >
                <Home :size="20" />
                <span>{{ t.home }}</span>
            </Link>
            <a
                v-if="phones.length > 0"
                :href="`tel:${phones[0].tel}`"
                class="flex flex-1 flex-col items-center gap-1 py-1 text-[11px] font-medium text-[color:var(--midnight)] transition-colors hover:text-[color:var(--orange)]"
            >
                <Phone :size="20" />
                <span>{{ t.callLabel }}</span>
            </a>
            <a
                href="#angebot"
                class="flex flex-1 flex-col items-center"
                :aria-label="t.cta"
            >
                <span
                    class="-mt-[65px] flex size-14 items-center justify-center rounded-full border-4 border-white bg-[var(--orange)] text-white shadow-[0_8px_24px_rgba(255,87,34,0.45)]"
                >
                    <ClipboardList :size="24" />
                </span>
            </a>
            <a
                href="#leistungen"
                class="flex flex-1 flex-col items-center gap-1 py-1 text-[11px] font-medium text-[color:var(--midnight)] transition-colors hover:text-[color:var(--orange)]"
            >
                <Truck :size="20" />
                <span>{{ t.services }}</span>
            </a>
            <button
                type="button"
                class="flex flex-1 flex-col items-center gap-1 py-1 text-[11px] font-medium text-[color:var(--midnight)] transition-colors hover:text-[color:var(--orange)]"
                @click="open = true"
            >
                <Menu :size="20" />
                <span>{{ t.menu }}</span>
            </button>
        </nav>
    </header>
    <div
        v-if="isStuck"
        class="mv-header-spacer"
        :style="{ height: navHeight + 'px' }"
        aria-hidden="true"
    ></div>
    </div>
</template>
