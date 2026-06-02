<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
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
import { getDefaultLocale, localizedUrl } from '@/lib/localizedUrl';

const props = defineProps<{
    locale: string;
}>();

const page = usePage();
const open = ref(false);

const otherLocale = computed(() => (props.locale === 'de' ? 'en' : 'de'));
const otherLocaleLabel = computed(() => (props.locale === 'de' ? 'EN' : 'DE'));

// Build the equivalent URL in the other locale. Strip any existing locale
// prefix (/de, /en), then re-add the locale segment only when the target is
// not the default locale.
const switchHref = computed(() => {
    const url = page.url ?? '/';
    const [pathOnly, query = ''] = url.split('?');
    const withoutPrefix = pathOnly.replace(/^\/(de|en)(?=\/|$)/, '') || '/';
    const target = localizedUrl(otherLocale.value, withoutPrefix);
    return query !== '' ? `${target}?${query}` : target;
});

const home = computed(() => localizedUrl(props.locale, '/'));

// Dynamic, locale-aware navigation.
const navItems = computed(() => [
    { label: props.locale === 'de' ? 'Startseite' : 'Home', href: home.value },
    { label: 'Blog', href: localizedUrl(props.locale, '/blog') },
    {
        label: props.locale === 'de' ? 'Seiten' : 'Pages',
        href: localizedUrl(props.locale, '/pages'),
    },
]);

// Locale-aware chrome copy for the top bar, CTA and the offcanvas.
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

const socials = [
    { label: 'Facebook', icon: Facebook, href: '#' },
    { label: 'Instagram', icon: Instagram, href: '#' },
    { label: 'Twitter', icon: Twitter, href: '#' },
    { label: 'LinkedIn', icon: Linkedin, href: '#' },
];

const socialLinks = [
    { abbr: 'FB', label: 'Facebook', href: '#' },
    { abbr: 'X', label: 'Twitter', href: '#' },
    { abbr: 'IN', label: 'Instagram', href: '#' },
    { abbr: 'LN', label: 'LinkedIn', href: '#' },
];

const emails = ['kontakt@moovato.de'];
const phones = [{ display: '+49 (0)30 2353 8660', tel: '+493023538660' }];
const address = 'Moovato HQ, Friedrichstraße 1, 10117 Berlin';

// Lock body scroll while the offcanvas is open + close on Escape.
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

onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);
    if (typeof document !== 'undefined') {
        document.body.style.overflow = '';
    }
});

// Keep the cached default locale in sync with whatever the server shares.
const sharedDefault = (page.props as Record<string, unknown>).defaultLocale;
if (typeof sharedDefault === 'string' && sharedDefault !== getDefaultLocale()) {
    import('@/lib/localizedUrl').then((m) => m.setDefaultLocale(sharedDefault));
}
</script>

<template>
    <header class="mv-header">
        <!-- ===== TOP BAR ===== -->
        <div class="topbar">
            <div
                class="topbar-inner relative z-[1] container-xl flex flex-wrap items-center justify-between gap-6"
            >
                <div class="topbar-welcome inline-flex items-center gap-2">
                    <span class="ico"><Clock :size="12" /></span>
                    <strong>{{ t.openingHours }}:</strong> {{ t.hours }}
                    <div class="topbar-socials">
                        <a
                            v-for="s in socials"
                            :key="s.label"
                            :href="s.href"
                            :aria-label="s.label"
                        >
                            <component :is="s.icon" :size="12" />
                        </a>
                    </div>
                </div>
                <div class="topbar-contacts">
                    <div class="item">
                        <span class="ico"><Mail :size="12" /></span>
                        {{ t.emailLabel }}:
                        <a href="mailto:kontakt@moovato.de"
                            >kontakt@moovato.de</a
                        >
                    </div>
                    <div class="item">
                        <span class="ico"><Phone :size="12" /></span>
                        {{ t.callLabel }}:
                        <a href="tel:+493023538660">+49 (0)30 2353 8660</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== NAVIGATION (sticky) ===== -->
        <nav
            class="sticky top-0 z-40 border-b border-[rgba(15,23,42,0.06)] bg-white py-[12px]"
        >
            <div class="container-xl flex items-center justify-between gap-8">
                <Link :href="home" class="flex items-center no-underline">
                    <img
                        src="/logo.svg"
                        alt="Moovato"
                        class=""
                        width="300"
                        height="36"
                    />
                </Link>

                <ul class="hidden list-none gap-9 lg:flex">
                    <li v-for="item in navItems" :key="item.href">
                        <Link :href="item.href" class="mv-nav-link">
                            {{ item.label }}
                        </Link>
                    </li>
                </ul>

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

                    <!-- Big nav -->
                    <nav class="mv-offcanvas__nav">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            @click="open = false"
                        >
                            {{ item.label }}
                        </Link>
                        <a href="#angebot" @click="open = false">{{
                            t.letsTalk
                        }}</a>
                    </nav>
                </div>

                <!-- Footer row: language switch + socials -->
                <div class="mv-offcanvas__foot">
                    <Link
                        :href="switchHref"
                        class="mv-offcanvas__lang"
                        @click="open = false"
                    >
                        {{ props.locale.toUpperCase() }} /
                        {{ otherLocaleLabel }}
                    </Link>
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
                href="tel:+493023538660"
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
</template>
