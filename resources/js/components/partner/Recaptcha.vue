<script setup lang="ts">
/**
 * Google reCAPTCHA v2 (checkbox) wrapper.
 *
 * Loads api.js exactly once per page load, polls for the global
 * `grecaptcha.render` to become available, then renders the widget
 * into a div ref.
 */
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

type GrecaptchaAPI = {
    render: (
        el: HTMLElement,
        params: {
            sitekey: string;
            theme?: 'light' | 'dark';
            size?: 'normal' | 'compact' | 'invisible';
            callback?: (token: string) => void;
            'expired-callback'?: () => void;
            'error-callback'?: () => void;
        },
    ) => number;
    reset: (widgetId?: number) => void;
    getResponse: (widgetId?: number) => string;
};

declare global {
    interface Window {
        grecaptcha?: GrecaptchaAPI;
    }
}

const props = withDefaults(defineProps<{
    modelValue?: string;
    siteKey: string | null;
    theme?: 'light' | 'dark';
    locale?: string;
}>(), {
    modelValue: '',
    theme: 'light',
    locale: 'de',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'error'): void;
    (e: 'expired'): void;
}>();

const widgetEl = ref<HTMLDivElement | null>(null);
const widgetId = ref<number | null>(null);
const loading = ref(true);

function ensureScript(locale: string): void {
    if (document.querySelector<HTMLScriptElement>('script[data-moovato-recaptcha]')) {
        return;
    }
    const script = document.createElement('script');
    script.src = `https://www.google.com/recaptcha/api.js?render=explicit&hl=${encodeURIComponent(locale)}`;
    script.async = true;
    script.defer = true;
    script.dataset.moovatoRecaptcha = 'true';
    document.head.appendChild(script);
}

function renderWhenReady(attempt = 0): void {
    if (!props.siteKey || !widgetEl.value) return;

    if (widgetId.value !== null) return;

    if (window.grecaptcha && typeof window.grecaptcha.render === 'function') {
        try {
            widgetId.value = window.grecaptcha.render(widgetEl.value, {
                sitekey: props.siteKey,
                theme: props.theme,
                callback: (token: string) => emit('update:modelValue', token),
                'expired-callback': () => {
                    emit('update:modelValue', '');
                    emit('expired');
                },
                'error-callback': () => {
                    emit('update:modelValue', '');
                    emit('error');
                },
            });
            loading.value = false;
        } catch (e) {
            console.error('reCAPTCHA render failed:', e);
            loading.value = false;
        }
        return;
    }

    if (attempt >= 150) {
        console.error('reCAPTCHA: grecaptcha.render never became available');
        loading.value = false;
        return;
    }

    setTimeout(() => renderWhenReady(attempt + 1), 100);
}

function reset(): void {
    if (widgetId.value !== null && window.grecaptcha) {
        try { window.grecaptcha.reset(widgetId.value); } catch { /* noop */ }
    }
}

watch(
    () => props.modelValue,
    (v) => {
        if (v === '' && widgetId.value !== null) {
            reset();
        }
    },
);

defineExpose({ reset });

onMounted(() => {
    if (!props.siteKey) {
        loading.value = false;
        return;
    }
    ensureScript(props.locale);
    renderWhenReady();
});

onBeforeUnmount(() => {
    if (widgetId.value !== null && window.grecaptcha) {
        try { window.grecaptcha.reset(widgetId.value); } catch { /* noop */ }
    }
    widgetId.value = null;
});

const disabled = computed(() => !props.siteKey);
</script>

<template>
    <div v-if="!disabled">
        <div ref="widgetEl" class="mv-recaptcha-widget"></div>
        <p v-if="loading" class="mt-1 text-[11px] text-[var(--slate-light)]">
            {{ props.locale === 'de' ? 'reCAPTCHA wird geladen…' : 'Loading reCAPTCHA…' }}
        </p>
    </div>
</template>

<style scoped>
.mv-recaptcha-widget {
    max-width: 100%;
    overflow-x: auto;
    overflow-y: visible;
}
</style>
