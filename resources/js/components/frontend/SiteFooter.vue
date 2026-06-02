<script setup lang="ts">
<<<<<<< HEAD
import { Link } from '@inertiajs/vue3';
import {
    ChevronsRight,
    Clock,
    Facebook,
    Linkedin,
    Mail,
    MapPin,
    Phone,
    Twitter,
=======
import { Link, usePage } from '@inertiajs/vue3';
import {
    FacebookIcon,
    InstagramIcon,
    LinkedinIcon,
    Mail,
    MapPin,
    MessageCircle,
    Phone,
    TwitterIcon,
>>>>>>> d01d026c73dfad050478bd6d20b764e3c2d3b6b7
} from 'lucide-vue-next';
import { computed } from 'vue';
import { localizedUrl } from '@/lib/localizedUrl';
import type { MenuNode } from './menu-types';

const props = defineProps<{
    locale: string;
}>();

const labels = computed(() => {
    const de = props.locale === 'de';
    return {
        aboutUs: de ? 'Über uns' : 'About Us',
        contactInfo: de ? 'Kontakt' : 'Contact Info',
        whatsapp: 'WhatsApp:',
        noLinks: de
            ? 'Noch keine Einträge — bitte im Navigationsmanagement hinzufügen.'
            : 'No items yet — add some in Navigation Management.',
        copyright: de
            ? 'Alle Rechte vorbehalten.'
            : 'All rights reserved.',
        privacy: de ? 'Datenschutzerklärung' : 'Privacy Policy',
        terms: de ? 'AGB' : 'Terms & Conditions',
    };
});

type SiteSettings = {
    about_text: string | null;
    address: string | null;
    phone: string | null;
    email: string | null;
    whatsapp: string | null;
    facebook_url: string | null;
    twitter_url: string | null;
    linkedin_url: string | null;
    instagram_url: string | null;
};

const page = usePage();
const year = new Date().getFullYear();

<<<<<<< HEAD
const t = computed(() =>
    props.locale === 'de'
        ? {
              tagline:
                  'Moovato — Berlins modernes Umzugsunternehmen. Stressfrei, versichert und zum Festpreis.',
              follow: 'Folgen:',
              usefulLinks: 'Nützliche Links',
              getInTouch: 'Kontakt',
              mobile: 'Telefon',
              email: 'E-Mail',
              address: 'Adresse',
              openingHours: 'Öffnungszeiten',
              hours: 'Mo–Fr 8–18 Uhr',
              rights: 'Alle Rechte vorbehalten.',
          }
        : {
              tagline:
                  "Moovato — Berlin's modern moving company. Stress-free, insured and at a fixed price.",
              follow: 'Follow On:',
              usefulLinks: 'Useful Links',
              getInTouch: 'Get in Touch',
              mobile: 'Phone',
              email: 'Email',
              address: 'Address',
              openingHours: 'Opening hours',
              hours: 'Mon–Fri 8am–6pm',
              rights: 'All Rights Reserved.',
          },
);

// Useful links — labels from the design, mapped to real routes where they exist.
const usefulLinks = computed(() => [
    {
        label: props.locale === 'de' ? 'Hilfe-Center' : 'Help Center',
        href: '#',
    },
    {
        label: props.locale === 'de' ? 'Über uns' : 'About Us',
        href: localizedUrl(props.locale, '/pages'),
    },
    { label: props.locale === 'de' ? 'Kontakt' : 'Contact Us', href: '#' },
    {
        label: props.locale === 'de' ? 'Partner werden' : 'Become A Partner',
        href: '#',
    },
    { label: 'Blog', href: localizedUrl(props.locale, '/blog') },
    {
        label: props.locale === 'de' ? 'Privatumzug' : 'Private Moving',
        href: '#',
    },
    { label: props.locale === 'de' ? 'Büroumzug' : 'Office Moving', href: '#' },
    {
        label: props.locale === 'de' ? 'Möbelmontage' : 'Furniture Assembly',
        href: '#',
    },
    { label: props.locale === 'de' ? 'Einlagerung' : 'Storage', href: '#' },
]);

const socials = [
    { label: 'Twitter', icon: Twitter, href: '#' },
    { label: 'Facebook', icon: Facebook, href: '#' },
    { label: 'LinkedIn', icon: Linkedin, href: '#' },
];
</script>

<template>
    <footer class="mv-footer">
        <div
            class="container-xl hidden gap-y-10 pt-16 pb-12 md:grid md:grid-cols-2 lg:grid-cols-[1.2fr_1.4fr_1fr] lg:gap-x-14"
        >
            <!-- Brand -->
            <div class="mv-footer__col">
                <Link
                    :href="localizedUrl(locale, '/')"
                    class="mv-footer__brand-logo"
                >
                    <img
                        src="/logo.svg"
                        alt="Moovato"
                        class="mv-footer__logo"
                        width="166"
                        height="40"
                    />
                </Link>

                <p class="mv-footer__tagline">{{ t.tagline }}</p>

                <div class="mv-footer__follow">
                    <span class="mv-footer__follow-label">{{ t.follow }}</span>
                    <span class="mv-footer__divider"></span>
                    <span class="mv-footer__socials">
=======
const footerColumns = computed<MenuNode[]>(() => {
    const m = (page.props as Record<string, unknown>).footerMenu;
    return Array.isArray(m) ? (m as MenuNode[]) : [];
});

const settings = computed<SiteSettings>(() => {
    const s = (page.props as Record<string, unknown>).siteSettings;
    return (s ?? {}) as SiteSettings;
});

const whatsappHref = computed<string | null>(() => {
    const w = settings.value.whatsapp;
    return w && w.length > 0 ? `https://wa.me/${w}` : null;
});

const phoneHref = computed<string | null>(() => {
    const p = settings.value.phone;
    if (!p) return null;
    const digits = p.replace(/[^\d+]/g, '');
    return digits.length > 0 ? `tel:${digits}` : null;
});

// Gmail compose URL instead of mailto: so clicking goes directly to the user's
// Gmail inbox in a new tab (per spec). If you ever want to fall back to the
// system mail client, swap this for `mailto:${e}`.
const emailHref = computed<string | null>(() => {
    const e = settings.value.email;
    return e
        ? `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(e)}`
        : null;
});

// Address becomes a Google Maps search link. We URL-encode the full address so
// commas/spaces survive the round-trip and the maps page lands on the right pin.
const addressHref = computed<string | null>(() => {
    const a = settings.value.address;
    return a
        ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(a)}`
        : null;
});

// Social icons: only render the ones the admin has actually filled in.
const socials = computed(() => {
    const s = settings.value;
    const out: Array<{ label: string; href: string; icon: typeof FacebookIcon }> = [];
    if (s.facebook_url) out.push({ label: 'Facebook', href: s.facebook_url, icon: FacebookIcon });
    if (s.twitter_url) out.push({ label: 'Twitter', href: s.twitter_url, icon: TwitterIcon });
    if (s.linkedin_url) out.push({ label: 'LinkedIn', href: s.linkedin_url, icon: LinkedinIcon });
    if (s.instagram_url) out.push({ label: 'Instagram', href: s.instagram_url, icon: InstagramIcon });
    return out;
});
</script>

<template>
    <footer class="border-t border-border/60 bg-background/80 text-muted-foreground backdrop-blur">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">
                <!-- About Us -->
                <div>
                    <h3 class="text-base font-semibold text-foreground">
                        {{ labels.aboutUs }}
                    </h3>
                    <p
                        v-if="settings.about_text"
                        class="mt-4 text-sm leading-relaxed text-muted-foreground"
                    >
                        {{ settings.about_text }}
                    </p>
                    <div v-if="socials.length > 0" class="mt-6 flex items-center gap-3">
>>>>>>> d01d026c73dfad050478bd6d20b764e3c2d3b6b7
                        <a
                            v-for="s in socials"
                            :key="s.label"
                            :href="s.href"
<<<<<<< HEAD
                            :aria-label="s.label"
                            class="mv-footer__social"
                        >
                            <component :is="s.icon" :size="16" />
                        </a>
                    </span>
                </div>
            </div>

            <!-- Useful Links -->
            <div class="mv-footer__col">
                <h2 class="mv-footer__heading">{{ t.usefulLinks }}</h2>
                <ul class="mv-footer__links">
                    <li v-for="item in usefulLinks" :key="item.label">
                        <component
                            :is="item.href.startsWith('#') ? 'a' : Link"
                            :href="item.href"
                            class="mv-footer__link"
                        >
                            <ChevronsRight class="ico" :size="16" />
                            {{ item.label }}
                        </component>
                    </li>
                </ul>
            </div>

            <!-- Get in touch -->
            <div class="mv-footer__col">
                <h2 class="mv-footer__heading">{{ t.getInTouch }}</h2>
                <ul class="mv-footer__contact">
                    <li>
                        <span class="mv-footer__contact-ico"
                            ><Phone :size="18"
                        /></span>
                        <span>
                            <span class="mv-footer__contact-label">{{
                                t.mobile
                            }}</span>
                            <a href="tel:+493023538660">+49 (0)30 2353 8660</a>
                        </span>
                    </li>
                    <li>
                        <span class="mv-footer__contact-ico"
                            ><Mail :size="18"
                        /></span>
                        <span>
                            <span class="mv-footer__contact-label">{{
                                t.email
                            }}</span>
                            <a href="mailto:kontakt@moovato.de"
                                >kontakt@moovato.de</a
                            >
                        </span>
                    </li>
                    <li>
                        <span class="mv-footer__contact-ico"
                            ><Clock :size="18"
                        /></span>
                        <span>
                            <span class="mv-footer__contact-label">{{
                                t.openingHours
                            }}</span>
                            <span class="mv-footer__contact-text">{{
                                t.hours
                            }}</span>
                        </span>
                    </li>
                    <li>
                        <span class="mv-footer__contact-ico"
                            ><MapPin :size="18"
                        /></span>
                        <span>
                            <span class="mv-footer__contact-label">{{
                                t.address
                            }}</span>
                            <span class="mv-footer__contact-text">
                                Friedrichstraße 1, 10117 Berlin
                            </span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="mv-footer__bar">
            <div
                class="container-xl flex flex-col items-center justify-between gap-5 py-3 text-center sm:flex-row sm:items-center sm:text-left"
            >
                <p class="mv-footer__copy">
                    © Copyrights {{ year }} - <strong>Moovato</strong>
                    {{ t.rights }}
                </p>
                <a
                    class="heart"
                    style="color: #fff"
                    title="Webdesign"
                    href="https://listandsell.de/"
                    target="_blank"
                    rel="noreferrer noopener"
                >
                    Webdesign mit <span class="heart__ico">❤</span> von List
                    &amp; Sell GmbH
                </a>
=======
                            target="_blank"
                            rel="noopener noreferrer"
                            :aria-label="s.label"
                            class="inline-flex size-9 items-center justify-center rounded-full border border-border bg-muted text-muted-foreground transition hover:border-foreground hover:text-foreground"
                        >
                            <component :is="s.icon" class="size-4" />
                        </a>
                    </div>
                </div>

                <div v-for="column in footerColumns" :key="column.id">
                    <h3 class="text-base font-semibold text-foreground">
                        {{ column.label }}
                    </h3>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li v-for="item in column.children" :key="item.id">
                            <a
                                v-if="item.url && item.open_in_new_tab"
                                :href="item.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-muted-foreground transition hover:text-foreground"
                            >{{ item.label }}</a>
                            <Link
                                v-else-if="item.url"
                                :href="item.url"
                                class="text-muted-foreground transition hover:text-foreground"
                            >{{ item.label }}</Link>
                            <span v-else class="text-muted-foreground">
                                {{ item.label }}
                            </span>
                        </li>
                        <li
                            v-if="column.children.length === 0"
                            class="text-xs italic text-muted-foreground/70"
                        >
                            {{ labels.noLinks }}
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-base font-semibold text-foreground">
                        {{ labels.contactInfo }}
                    </h3>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li v-if="addressHref" class="flex items-start gap-3">
                            <MapPin class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                            <a
                                :href="addressHref"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-muted-foreground transition hover:text-foreground"
                            >{{ settings.address }}</a>
                        </li>
                        <li v-if="phoneHref" class="flex items-start gap-3">
                            <Phone class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                            <a
                                :href="phoneHref"
                                class="text-muted-foreground transition hover:text-foreground"
                            >{{ settings.phone }}</a>
                        </li>
                        <li v-if="emailHref" class="flex items-start gap-3">
                            <Mail class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                            <a
                                :href="emailHref"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-muted-foreground transition hover:text-foreground"
                            >{{ settings.email }}</a>
                        </li>
                        <li v-if="whatsappHref" class="flex items-start gap-3">
                            <MessageCircle class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                            <a
                                :href="whatsappHref"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-muted-foreground transition hover:text-foreground"
                            >{{ labels.whatsapp }} +{{ settings.whatsapp }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="mt-10 flex flex-col items-start justify-between gap-3 border-t border-border/60 pt-6 text-xs text-muted-foreground sm:flex-row sm:items-center"
            >
                <p>© {{ year }} Moovato. {{ labels.copyright }}</p>
                <nav class="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <Link
                        :href="localizedUrl(locale, '/privacy-policy')"
                        class="transition hover:text-foreground"
                    >{{ labels.privacy }}</Link>
                    <span class="text-border">|</span>
                    <Link
                        :href="localizedUrl(locale, '/terms-and-conditions')"
                        class="transition hover:text-foreground"
                    >{{ labels.terms }}</Link>
                </nav>
>>>>>>> d01d026c73dfad050478bd6d20b764e3c2d3b6b7
            </div>
        </div>
    </footer>
</template>
