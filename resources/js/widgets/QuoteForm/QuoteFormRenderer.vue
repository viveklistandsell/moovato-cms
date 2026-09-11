<script setup lang="ts">
import {
    Building2,
    Check,
    CheckCircle2,
    FileText,
    Home,
    Map,
    Package,
    Send,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';

type Data = {
    eyebrow?: string;
    heading?: string;
    lead?: string;
    benefits?: string[];
    form_title?: string;
    form_subtitle?: string;
    success_title?: string;
    success_text?: string;
};

const props = defineProps<{ settings: Record<string, unknown>; data: Data }>();

const benefits = computed(() => props.data.benefits ?? []);

// ----- Single-step form state (static fields, client-side only) -----
const submitted = ref(false);

const moveTypes = [
    { value: 'Privat', label: 'Privat', icon: Home },
    { value: 'Gewerbe', label: 'Gewerbe', icon: Building2 },
    { value: 'Fernumzug', label: 'Fernumzug', icon: Map },
    { value: 'Spezialtransport', label: 'Spezial', icon: Package },
];

const form = reactive({
    umzugsart: 'Privat',
    umzugsdatum: '',
    flexibel: false,
});

const errors = ref<Record<string, string>>({});

// ----- Scroll-driven selection: active card advances as the section scrolls through the viewport -----
const sectionRef = ref<HTMLElement | null>(null);
let scrollTicking = false;

function syncSelectionToScroll(): void {
    const el = sectionRef.value;

    if (!el) {
        return;
    }

    const rect = el.getBoundingClientRect();
    const vh = window.innerHeight || document.documentElement.clientHeight;
    const anchor = rect.top + rect.height / 2;
    const start = vh * 0.75;
    const end = vh * 0.25;
    const progress = (start - anchor) / (start - end);
    const clamped = Math.min(Math.max(progress, 0), 0.999);
    const index = Math.floor(clamped * moveTypes.length);

    form.umzugsart = moveTypes[index].value;
}

function onScroll(): void {
    if (scrollTicking) {
        return;
    }

    scrollTicking = true;
    window.requestAnimationFrame(() => {
        syncSelectionToScroll();
        scrollTicking = false;
    });
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    syncSelectionToScroll();
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', onScroll);
});

function submit(): void {
    const e: Record<string, string> = {};

    if (!form.umzugsdatum) {
        e.umzugsdatum = 'Bitte ein Wunschdatum wählen.';
    }

    errors.value = e;

    if (Object.keys(e).length === 0) {
        submitted.value = true;
    }
}
</script>

<template>
    <section
        ref="sectionRef"
        v-reveal
        class="mv-quote section-py"
        id="angebot"
    >
        <div
            class="container-xl grid grid-cols-1 items-start gap-y-9 lg:grid-cols-2 lg:gap-x-10"
        >
            <!-- LEFT: marketing aside (editable) -->
            <aside class="mv-quote__aside">
                <span class="v7-eyebrow"
                    ><FileText :size="16" /> {{ data.eyebrow }}</span
                >
                <h2 class="mv-quote__h1 mv-section-heading">{{ data.heading }}</h2>
                <p class="lead">{{ data.lead }}</p>
                <ul>
                    <li v-for="(b, i) in benefits" :key="i">
                        <CheckCircle2 :size="20" /> {{ b }}
                    </li>
                </ul>
            </aside>

            <!-- RIGHT: form card (static) -->
            <div class="form-card">
                <div class="v7-form-head">
                    <h3>{{ data.form_title }}</h3>
                    <p>{{ data.form_subtitle }}</p>
                </div>

                <div class="v7-form-body">
                    <template v-if="!submitted">
                        <form novalidate @submit.prevent="submit">
                            <fieldset class="form-step">
                                <div class="field">
                                    <span class="label">Umzugsart</span>
                                    <div
                                        class="grid grid-cols-2 gap-2.5 sm:grid-cols-4"
                                    >
                                        <label
                                            v-for="m in moveTypes"
                                            :key="m.value"
                                            class="radio-card"
                                        >
                                            <input
                                                type="radio"
                                                name="umzugsart"
                                                :value="m.value"
                                                v-model="form.umzugsart"
                                            />
                                            <span class="radio-card__body">
                                                <component
                                                    :is="m.icon"
                                                    :size="20"
                                                />
                                                {{ m.label }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="mvq-datum"
                                        >Wunschtermin</label
                                    >
                                    <input
                                        id="mvq-datum"
                                        class="input"
                                        type="date"
                                        v-model="form.umzugsdatum"
                                    />
                                    <p
                                        v-if="errors.umzugsdatum"
                                        class="error-text"
                                    >
                                        {{ errors.umzugsdatum }}
                                    </p>
                                </div>
                                <div class="field">
                                    <label class="check">
                                        <input
                                            type="checkbox"
                                            v-model="form.flexibel"
                                        />
                                        <span>Datum ist flexibel</span>
                                    </label>
                                </div>
                                <div class="form-actions">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Kostenloses Angebot anfordern
                                        <Send :size="18" />
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </template>

                    <!-- Success -->
                    <div v-else class="form-success">
                        <span class="form-success__icon"
                            ><Check :size="28"
                        /></span>
                        <h3 class="mv-quote__h2">{{ data.success_title }}</h3>
                        <p class="muted">{{ data.success_text }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
