<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{ locale: string }>();

const page = usePage();

type SiteSettings = {
    phone?: string | null;
    email?: string | null;
    whatsapp?: string | null;
};
const siteSettings = computed<SiteSettings>(
    () => (page.props.siteSettings as SiteSettings | undefined) ?? {},
);

const t = computed(() =>
    props.locale === 'de'
        ? {
              contact: 'Kontakt',
              call: 'Anrufen',
              email: 'E-Mail',
              whatsapp: 'WhatsApp',
          }
        : {
              contact: 'Contact',
              call: 'Call',
              email: 'Email',
              whatsapp: 'WhatsApp',
          },
);

const phoneTel = computed<string | null>(() => {
    const p = siteSettings.value.phone;
    return p ? p.replace(/[^\d+]/g, '') : null;
});
const email = computed<string | null>(() => siteSettings.value.email ?? null);
const whatsappLink = computed<string | null>(() => {
    const w = siteSettings.value.whatsapp;
    if (!w) return null;
    return `https://wa.me/${w.replace(/[^\d]/g, '')}`;
});
</script>

<template>
    <div
        class="mv-floating-btns"
        :aria-label="locale === 'de' ? 'Schnellkontakt' : 'Quick contact'"
    >
    <div class="mv-floating-btns-bg"></div>
        <a class="mv-float-btn" href="#kontakt" :aria-label="t.contact">
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path
                    d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
                />
            </svg>
            <span>{{ t.contact }}</span>
        </a>

        <a
            v-if="phoneTel"
            class="mv-float-btn"
            :href="`tel:${phoneTel}`"
            :aria-label="t.call"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path
                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"
                />
            </svg>
            <span>{{ t.call }}</span>
        </a>

        <a
            v-if="email"
            class="mv-float-btn"
            :href="`mailto:${email}`"
            :aria-label="t.email"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <rect x="2" y="4" width="20" height="16" rx="2" />
                <path d="m22 7-10 6L2 7" />
            </svg>
            <span>{{ t.email }}</span>
        </a>

        <a
            v-if="whatsappLink"
            class="mv-float-btn mv-float-btn--wa"
            :href="whatsappLink"
            target="_blank"
            rel="noopener"
            :aria-label="t.whatsapp"
        >
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path
                    d="M17.5 14.4c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51l-.57-.01c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.06 2.87 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35zM12 2a10 10 0 0 0-8.5 15.3L2 22l4.8-1.5A10 10 0 1 0 12 2z"
                />
            </svg>
            <span>{{ t.whatsapp }}</span>
        </a>
    </div>
</template>
