<script setup lang="ts">
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
                        <a
                            v-for="s in socials"
                            :key="s.label"
                            :href="s.href"
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
            </div>
        </div>
    </footer>
</template>
