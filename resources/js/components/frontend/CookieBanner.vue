<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import * as CookieConsent from 'vanilla-cookieconsent';
import 'vanilla-cookieconsent/dist/cookieconsent.css';

const BANNER_VERSION = 'v1';

const page = usePage();

type SitePropsShape = {
    locale?: string;
};

const locale = (): 'de' | 'en' =>
    ((page.props as SitePropsShape).locale ?? 'de').startsWith('en')
        ? 'en'
        : 'de';

async function record(
    action: 'accept_all' | 'reject_all' | 'custom' | 'revoke',
    categories: string[],
): Promise<void> {
    try {
        await fetch('/cookie-consent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                action,
                categories,
                locale: locale(),
                banner_version: BANNER_VERSION,
            }),
        });
    } catch {

    }
}

// Map vanilla-cookieconsent's API surface to our 4 audit actions.
function actionFor(
    cookie: CookieConsent.CookieValue | undefined,
    accepted: string[],
): 'accept_all' | 'reject_all' | 'custom' | 'revoke' {
    if (!cookie) {
        return 'custom';
    }
    const optional = ['analytics', 'marketing'];
    const acceptedOptional = optional.filter((c) => accepted.includes(c));
    if (acceptedOptional.length === optional.length) {
        return 'accept_all';
    }
    if (acceptedOptional.length === 0) {
        return 'reject_all';
    }
    return 'custom';
}

onMounted(() => {
    CookieConsent.run({
        cookie: {
            name: 'mv_consent',
            expiresAfterDays: 365,
        },
        guiOptions: {
            consentModal: {
                layout: 'box wide',
                position: 'bottom right',
                equalWeightButtons: true,
                flipButtons: false,
            },
            preferencesModal: {
                layout: 'box',
                position: 'right',
                equalWeightButtons: true,
                flipButtons: false,
            },
        },
        categories: {
            necessary: {
                enabled: true,
                readOnly: true,
            },
            analytics: {
                enabled: false,
                readOnly: false,
                autoClear: {
                    cookies: [
                        { name: /^_ga/ },
                        { name: '_gid' },
                        { name: /^_gat/ },
                    ],
                },
                services: {
                    google_analytics: {
                        label: 'Google Analytics',
                    },
                    google_tag_manager: {
                        label: 'Google Tag Manager',
                    },
                },
            },
            marketing: {
                enabled: false,
                readOnly: false,
                autoClear: {
                    cookies: [{ name: '_fbp' }, { name: /^_fbc/ }],
                },
                services: {
                    meta_pixel: {
                        label: 'Meta (Facebook) Pixel',
                    },
                },
            },
        },
        language: {
            default: locale(),
            autoDetect: 'browser',
            translations: {
                de: {
                    consentModal: {
                        title: 'Wir respektieren Ihre Privatsphäre',
                        description:
                            'Diese Website verwendet Cookies, die für den Betrieb notwendig sind, sowie optionale Analyse- und Marketing-Cookies. Sie können Ihre Auswahl jederzeit anpassen.',
                        acceptAllBtn: 'Alle akzeptieren',
                        acceptNecessaryBtn: 'Alle ablehnen',
                        showPreferencesBtn: 'Einstellungen',
                        footer:
                            '<a href="/datenschutz">Datenschutz</a> · <a href="/impressum">Impressum</a>',
                    },
                    preferencesModal: {
                        title: 'Cookie-Einstellungen',
                        acceptAllBtn: 'Alle akzeptieren',
                        acceptNecessaryBtn: 'Alle ablehnen',
                        savePreferencesBtn: 'Auswahl speichern',
                        closeIconLabel: 'Schließen',
                        sections: [
                            {
                                title: 'Cookie-Nutzung auf moovato.de',
                                description:
                                    'Wir verwenden Cookies, um die grundlegende Funktion der Website sicherzustellen und – mit Ihrer Zustimmung – um zu verstehen, wie Sie die Seite nutzen und unser Angebot zu verbessern.',
                            },
                            {
                                title: 'Notwendig',
                                description:
                                    'Diese Cookies sind technisch erforderlich (Sitzung, CSRF-Schutz, Spracheinstellung). Ohne sie funktioniert die Website nicht.',
                                linkedCategory: 'necessary',
                            },
                            {
                                title: 'Analyse',
                                description:
                                    'Helfen uns zu verstehen, wie Besucher die Website nutzen (Google Analytics, Google Tag Manager). IP-Adressen werden anonymisiert.',
                                linkedCategory: 'analytics',
                            },
                            {
                                title: 'Marketing',
                                description:
                                    'Werden für Werbezwecke eingesetzt, z. B. um Ihnen relevantere Anzeigen zu zeigen (Meta-Pixel).',
                                linkedCategory: 'marketing',
                            },
                            {
                                title: 'Mehr erfahren',
                                description:
                                    'Details finden Sie in unserer <a href="/datenschutz">Datenschutzerklärung</a>.',
                            },
                        ],
                    },
                },
                en: {
                    consentModal: {
                        title: 'We respect your privacy',
                        description:
                            'This site uses cookies that are necessary for it to work, plus optional analytics and marketing cookies. You can change your choice at any time.',
                        acceptAllBtn: 'Accept all',
                        acceptNecessaryBtn: 'Reject all',
                        showPreferencesBtn: 'Settings',
                        footer:
                            '<a href="/en/privacy">Privacy</a> · <a href="/en/imprint">Imprint</a>',
                    },
                    preferencesModal: {
                        title: 'Cookie settings',
                        acceptAllBtn: 'Accept all',
                        acceptNecessaryBtn: 'Reject all',
                        savePreferencesBtn: 'Save preferences',
                        closeIconLabel: 'Close',
                        sections: [
                            {
                                title: 'Cookies on moovato.de',
                                description:
                                    'We use cookies to keep the site running and — with your permission — to understand how you use it and improve our service.',
                            },
                            {
                                title: 'Necessary',
                                description:
                                    'Technically required (session, CSRF protection, language preference). The site does not work without these.',
                                linkedCategory: 'necessary',
                            },
                            {
                                title: 'Analytics',
                                description:
                                    'Help us understand how visitors use the site (Google Analytics, Google Tag Manager). IP addresses are anonymised.',
                                linkedCategory: 'analytics',
                            },
                            {
                                title: 'Marketing',
                                description:
                                    'Used for advertising, e.g. to show you more relevant ads (Meta Pixel).',
                                linkedCategory: 'marketing',
                            },
                            {
                                title: 'Learn more',
                                description:
                                    'Read more in our <a href="/en/privacy">Privacy Policy</a>.',
                            },
                        ],
                    },
                },
            },
        },
        onConsent: ({ cookie }) => {
            const accepted = cookie.categories ?? [];
            record(actionFor(cookie, accepted), accepted);
        },
        onChange: ({ cookie }) => {
            const accepted = cookie.categories ?? [];
            record(actionFor(cookie, accepted), accepted);
        },
    });
    const handler = (event: Event): void => {
        const target = event.target as HTMLElement | null;
        if (target?.closest('[data-cookie-settings]')) {
            event.preventDefault();
            CookieConsent.showPreferences();
        }
    };
    document.addEventListener('click', handler);
    cleanupHandler = handler;
});

let cleanupHandler: ((event: Event) => void) | null = null;

onUnmounted(() => {
    if (cleanupHandler) {
        document.removeEventListener('click', cleanupHandler);
    }
});
</script>

<template>
    <span class="hidden" aria-hidden="true" />
</template>
