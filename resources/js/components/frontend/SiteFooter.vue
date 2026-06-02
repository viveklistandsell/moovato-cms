<script setup lang="ts">
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
} from 'lucide-vue-next';
import { computed } from 'vue';
import { localizedUrl } from '@/lib/localizedUrl';

const props = defineProps<{
    locale: string;
}>();

const year = new Date().getFullYear();

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
                        <a
                            v-for="s in socials"
                            :key="s.label"
                            :href="s.href"
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
            </div>
        </div>
    </footer>
</template>
