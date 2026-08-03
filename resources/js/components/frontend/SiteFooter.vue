<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    ChevronsRight,
    Clock,
    Facebook,
    Instagram,
    Linkedin,
    Mail,
    MapPin,
    Phone,
    Twitter,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { localizedUrl } from '@/lib/localizedUrl';

const page = usePage();

type SiteLayout = { footer_copyright_auto_year?: boolean };
const siteLayout = computed<SiteLayout>(
    () => (page.props.siteLayout as SiteLayout | undefined) ?? {},
);

const props = defineProps<{
    locale: string;
}>();

const LAUNCH_YEAR = 2026;
const year = computed<number>(() =>
    siteLayout.value.footer_copyright_auto_year === false
        ? LAUNCH_YEAR
        : new Date().getFullYear(),
);

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
            cookieSettings: 'Cookie-Einstellungen',
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
            cookieSettings: 'Cookie Settings',
        },
);

// Footer columns — top-level items in the admin-managed `footer` menu
// become column headings; their children are the actual links. Mirrors
// the pattern used in Navigation Management. When the admin hasn't
// configured a footer menu at all we fall back to a single column built
// from sensible defaults so the layout doesn't visually collapse.
type FooterMenuItem = {
    id: number;
    label: string;
    url: string | null;
    open_in_new_tab: boolean;
    css_class: string | null;
    children: FooterMenuItem[];
};

const footerMenu = computed<FooterMenuItem[]>(
    () => (page.props.footerMenu as FooterMenuItem[] | undefined) ?? [],
);

type FooterColumn = {
    heading: string;
    links: Array<{ label: string; href: string; newTab: boolean }>;
};

const footerColumns = computed<FooterColumn[]>(() => {
    if (footerMenu.value.length === 0) {
        return [
            {
                heading: props.locale === 'de' ? 'Schnelllinks' : 'Quick Links',
                links: [
                    {
                        label: props.locale === 'de' ? 'Startseite' : 'Home',
                        href: localizedUrl(props.locale, '/'),
                        newTab: false,
                    },
                    {
                        label: 'Blog',
                        href: localizedUrl(props.locale, '/blog'),
                        newTab: false,
                    },
                ],
            },
        ];
    }

    return footerMenu.value.map((column) => ({
        heading: column.label,
        links: (column.children ?? []).map((child) => ({
            label: child.label,
            href: child.url ?? '#',
            newTab: child.open_in_new_tab,
        })),
    }));
});

// Admin-managed settings shipped by HandleInertiaRequests::share().
// Brand, phone, email, address, socials and the logo all derive from
// these fields so the admin form fully controls the footer.
type SiteSettings = {
    site_name?: string | null;
    logo_light_url?: string | null;
    phone?: string | null;
    email?: string | null;
    address?: string | null;
    facebook_url?: string | null;
    twitter_url?: string | null;
    linkedin_url?: string | null;
    instagram_url?: string | null;
};
const siteSettings = computed<SiteSettings>(
    () => (page.props.siteSettings as SiteSettings | undefined) ?? {},
);

const brandName = computed<string>(() => siteSettings.value.site_name || 'Moovato');
const logoUrl = computed<string | null>(() => siteSettings.value.logo_light_url ?? null);

const phone = computed<{ display: string; tel: string } | null>(() => {
    const p = siteSettings.value.phone;
    if (!p) return null;
    return { display: p, tel: p.replace(/[^\d+]/g, '') };
});
const email = computed<string | null>(() => siteSettings.value.email ?? null);
const address = computed<string | null>(() => siteSettings.value.address ?? null);

// Only render the social icons the admin has actually configured.
const socials = computed<Array<{ label: string; icon: typeof Facebook; href: string }>>(() => {
    const s = siteSettings.value;
    const out: Array<{ label: string; icon: typeof Facebook; href: string }> = [];
    if (s.twitter_url) out.push({ label: 'Twitter', icon: Twitter, href: s.twitter_url });
    if (s.facebook_url) out.push({ label: 'Facebook', icon: Facebook, href: s.facebook_url });
    if (s.instagram_url) out.push({ label: 'Instagram', icon: Instagram, href: s.instagram_url });
    if (s.linkedin_url) out.push({ label: 'LinkedIn', icon: Linkedin, href: s.linkedin_url });
    return out;
});
</script>

<template>
    <footer class="mv-footer">
        <div :class="[
            'container-xl hidden gap-y-10 pt-16 pb-12 md:grid md:grid-cols-2 lg:gap-x-10',
            footerColumns.length >= 2
                ? 'lg:grid-cols-[1.5fr_1fr_1fr_1fr]'
                : 'lg:grid-cols-[1.2fr_1.4fr_1fr]',
        ]">
            <!-- Brand -->
            <div class="mv-footer__col">
                <Link :href="localizedUrl(locale, '/')" class="mv-footer__brand-logo">
                    <img :src="logoUrl ?? '/logo.svg'" :alt="brandName" class="mv-footer__logo" width="166"
                        height="80" />
                </Link>

                <p class="mv-footer__tagline">{{ t.tagline }}</p>

                <div class="mv-footer__follow">
                    <span class="mv-footer__follow-label">{{ t.follow }}</span>
                    <span class="mv-footer__divider"></span>
                    <span class="mv-footer__socials">
                        <a v-for="s in socials" :key="s.label" :href="s.href" :aria-label="s.label"
                            class="mv-footer__social">
                            <component :is="s.icon" :size="16" />
                        </a>
                    </span>
                </div>
            </div>

            <!-- Footer columns (driven by the admin's Navigation Management
                 → Footer Menu — top-level items become column headings, their
                 children are the links). New-tab items get a plain <a>; SPA
                 paths use <Link>; anything else (mailto:, tel:, #anchor,
                 absolute URLs) also falls back to <a>. -->
            <div v-for="column in footerColumns" :key="column.heading" class="mv-footer__col">
                <h2 class="mv-footer__heading">{{ column.heading }}</h2>
                <ul class="mv-footer__links">
                    <li v-for="link in column.links" :key="link.label">
                        <a v-if="link.newTab" :href="link.href" target="_blank" rel="noopener noreferrer"
                            class="mv-footer__link">
                            <ChevronsRight class="ico" :size="16" />
                            {{ link.label }}
                        </a>
                        <Link v-else-if="link.href.startsWith('/') && !link.href.startsWith('//')" :href="link.href"
                            class="mv-footer__link">
                            <ChevronsRight class="ico" :size="16" />
                            {{ link.label }}
                        </Link>
                        <a v-else :href="link.href" class="mv-footer__link">
                            <ChevronsRight class="ico" :size="16" />
                            {{ link.label }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Get in touch -->
            <div class="mv-footer__col">
                <h2 class="mv-footer__heading">{{ t.getInTouch }}</h2>
                <ul class="mv-footer__contact">
                    <li v-if="phone">
                        <span class="mv-footer__contact-ico">
                            <Phone :size="18" />
                        </span>
                        <span>
                            <span class="mv-footer__contact-label">{{
                                t.mobile
                                }}</span>
                            <a :href="`tel:${phone.tel}`">{{ phone.display }}</a>
                        </span>
                    </li>
                    <li v-if="email">
                        <span class="mv-footer__contact-ico">
                            <Mail :size="18" />
                        </span>
                        <span>
                            <span class="mv-footer__contact-label">{{
                                t.email
                                }}</span>
                            <a :href="`mailto:${email}`">{{ email }}</a>
                        </span>
                    </li>
                    <li>
                        <span class="mv-footer__contact-ico">
                            <Clock :size="18" />
                        </span>
                        <span>
                            <span class="mv-footer__contact-label">{{
                                t.openingHours
                                }}</span>
                            <span class="mv-footer__contact-text">{{
                                t.hours
                                }}</span>
                        </span>
                    </li>
                    <li v-if="address">
                        <span class="mv-footer__contact-ico">
                            <MapPin :size="18" />
                        </span>
                        <span>
                            <span class="mv-footer__contact-label">{{
                                t.address
                                }}</span>
                            <span class="mv-footer__contact-text">
                                {{ address }}
                            </span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="mv-footer__bar">
            <div
                class="container-xl flex flex-col items-center justify-between gap-5 py-3 text-center sm:flex-row sm:items-center sm:text-left">
                <p class="mv-footer__copy">
                    © Copyrights {{ year }} - <strong>{{ brandName }}</strong>
                    {{ t.rights }}
                    <span class="mx-1.5 opacity-50">·</span>
                    <button type="button"
                        class="inline cursor-pointer border-0 bg-transparent p-0 text-inherit underline underline-offset-[3px] opacity-85 transition-opacity hover:opacity-100 hover:text-[var(--orange)]"
                        data-cookie-settings>
                        {{ t.cookieSettings }}
                    </button>
                </p>
                <a class="heart" style="color: #fff" title="Webdesign" href="https://listandsell.de/" target="_blank"
                    rel="noreferrer noopener">
                    Webdesign mit <span class="heart__ico">❤</span> von List
                    &amp; Sell GmbH
                </a>
            </div>
        </div>
    </footer>
</template>
