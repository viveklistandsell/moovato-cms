<script setup lang="ts">
import { computed, ref } from 'vue';
import NextButton from '@/widgets/shared/NextButton.vue';

type Pin = {
    x?: number;
    y?: number;
    city?: string;
    label?: string;
    flag_path?: string | null;
    flag_url?: string | null;
};

type Settings = {
    image_path: string | null;
    image_url: string | null;
    pins?: Pin[];
};

type Data = {
    eyebrow?: string;
    heading?: string;
    subheading?: string;
    description?: string;
    button_label?: string;
    button_url?: string;
};

const props = defineProps<{
    settings: Settings;
    data: Data;
}>();

const search = ref('');

const pins = computed<Pin[]>(() => props.settings.pins ?? []);

function clamp(value: number | undefined): number {
    if (typeof value !== 'number' || Number.isNaN(value)) {
        return 50;
    }

    return Math.min(100, Math.max(0, value));
}

function edgeClass(value: number | undefined): string {
    const x = clamp(value);

    if (x <= 20) return 'is-left';
    if (x >= 80) return 'is-right';

    return '';
}
const selectedCity = ref<string | null>(null);

const suggestions = computed(() => {
    if (!search.value.trim()) return [];

    return pins.value.filter(pin =>
        pin.city?.toLowerCase().includes(search.value.toLowerCase())
    );
});

function selectCity(pin: Pin) {
    selectedCity.value = pin.city ?? null;
    search.value = pin.city ?? '';
}

function isMatched(pin: Pin) {
    return selectedCity.value === pin.city;
}

</script>

<template>
    <section v-reveal class="mv-map section-py">
        <div class="container-xl">
            <div
                v-if="data.eyebrow || data.heading || data.subheading || data.description"
                class="mx-auto max-w-2xl text-center"
            >
                <span
                    v-if="data.eyebrow"
                    class="mv-map-eyebrow"
                >
                    {{ data.eyebrow }}
                </span>

                <h2
                    v-if="data.heading"
                    class="mt-5 text-[var(--white)] mv-section-heading"
                >
                    {{ data.heading }}
                </h2>

                <div
                    v-if="data.description"
                    class="mv-rte mt-4 text-base leading-relaxed text-[var(--slate-light)]"
                    v-html="data.description"
                />

                <p
                    v-if="data.subheading"
                    class="mt-4 text-base text-[var(--slate-light)]"
                >
                    {{ data.subheading }}
                </p>
                <NextButton
                    v-if="data.button_label"
                    class="mt-6"
                    :label="data.button_label"
                    :href="data.button_url || '#'"
                />
            </div>

            <!-- Search -->
          <div class="mv-map-search">
    <input
        v-model="search"
        type="text"
        placeholder="Search city..."
        class="mv-map-search-input"
    />

    <div
        v-if="suggestions.length"
        class="mv-map-search-results"
    >
        <button
            v-for="(pin, index) in suggestions"
            :key="index"
            class="mv-map-search-item"
            @click="selectCity(pin)"
        >
            <img
                v-if="pin.flag_url"
                :src="pin.flag_url"
                :alt="pin.city"
                width="22"
                height="16"
            />

            <span>{{ pin.city }}</span>
        </button>
    </div>
</div>

            <div
                class="mv-map-canvas mx-auto max-w-3xl"
                :class="{ 'mv-map-canvas--empty': !settings.image_url }"
            >
                <img
                    v-if="settings.image_url"
                    :src="settings.image_url"
                    alt=""
                    class="mv-map-img"
                    loading="lazy"
                    decoding="async"
                />

                <button
                    v-for="(pin, i) in pins"
                    :key="i"
                    type="button"
                    class="mv-map-pin"
                    :class="[
                        edgeClass(pin.x),
                        { 'mv-map-pin--active': isMatched(pin) }
                    ]"
                    :style="{
                        left: clamp(pin.x) + '%',
                        top: clamp(pin.y) + '%'
                    }"
                    :aria-label="pin.city"
                >
                    <span
                        class="mv-map-dot"
                        aria-hidden="true"
                    ></span>

                    <span class="mv-map-card">
                        <span
                            v-if="pin.flag_url"
                            class="mv-map-flag"
                        >
                            <img
                                :src="pin.flag_url"
                                :alt="pin.city || ''"
                            />
                        </span>

                        <span
                            v-if="pin.city"
                            class="mv-map-city"
                        >
                            {{ pin.city }}
                        </span>

                        <span
                            v-if="pin.label"
                            class="mv-map-label"
                        >
                            {{ pin.label }}
                        </span>
                    </span>
                </button>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* ------------------------------
   Search
------------------------------ */
.mv-map-search {
    position: relative;
    max-width: 450px;
    margin: 1rem auto 0 !important;
}

.mv-map-search-results {
    position: absolute;
    left: 0;
    right: 0;
    top: calc(100% + 10px);
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,.15);
    z-index: 999;
    max-height: 280px;
    overflow-y: auto;
}

.mv-map-search-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: transparent;
    border: none;
    cursor: pointer;
    text-align: left;
    transition: .2s;
}

.mv-map-search-item:hover {
    background: #f5f5f5;
}

.mv-map-search-item img {
    border-radius: 3px;
    object-fit: cover;
}


.mv-map-search-input {
    width: 100%;
    max-width: 450px;
    padding: 14px 22px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    font-size: 15px;
    outline: none;
    transition: all .25s ease;
}

.mv-map-search-input::placeholder {
    color: rgba(255,255,255,.55);
}

.mv-map-search-input:focus {
    border-color: var(--primary, #ff5722);
    box-shadow: 0 0 0 4px rgba(255,87,34,.15);
}

/* ------------------------------
   Active Pin
------------------------------ */

.mv-map-pin--active {
    z-index: 20;
}
.mv-map-pin--active  .mv-map-card{
opacity: 1;
visibility: visible;
}
.mv-map-pin--active .mv-map-dot {
    background: var(--black);
    animation: pulse 1.6s infinite;
    transform: scale(1.4);
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255,87,34,.55);
    }
    70% {
        box-shadow: 0 0 0 14px rgba(255,87,34,0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255,87,34,0);
    }
}
</style>
<!-- <script setup lang="ts">
import { computed } from 'vue';

type Pin = {
    x?: number;
    y?: number;
    city?: string;
    label?: string;
    flag_path?: string | null;
    flag_url?: string | null;
};

type Settings = {
    image_path: string | null;
    image_url: string | null;
    pins?: Pin[];
};

type Data = {
    eyebrow?: string;
    heading?: string;
    subheading?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const pins = computed<Pin[]>(() => props.settings.pins ?? []);

function clamp(value: number | undefined): number {
    if (typeof value !== 'number' || Number.isNaN(value)) {
        return 50;
    }

    return Math.min(100, Math.max(0, value));
}

function edgeClass(value: number | undefined): string {
    const x = clamp(value);

    if (x <= 20) {
        return 'is-left';
    }

    if (x >= 80) {
        return 'is-right';
    }

    return '';
}
</script>

<template>
    <section class="mv-map section-py">
        <div class="container-xl">
            <div
                v-if="data.eyebrow || data.heading || data.subheading"
                class="mx-auto max-w-2xl text-center"
            >
                <span v-if="data.eyebrow" class="mv-map-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="mt-5 text-[var(--white)] mv-section-heading"
                >
                    {{ data.heading }}
                </h2>
                <p
                    v-if="data.subheading"
                    class="mt-4 text-base text-[var(--slate-light)]"
                >
                    {{ data.subheading }}
                </p>
            </div>

            <div
                class="mv-map-canvas mx-auto max-w-5xl"
                :class="{ 'mv-map-canvas--empty': !settings.image_url }"
            >
                <img
                    v-if="settings.image_url"
                    :src="settings.image_url"
                    alt=""
                    class="mv-map-img"
                    loading="lazy"
                    decoding="async"
                />

                <button
                    v-for="(pin, i) in pins"
                    :key="i"
                    type="button"
                    class="mv-map-pin"
                    :class="edgeClass(pin.x)"
                    :style="{
                        left: clamp(pin.x) + '%',
                        top: clamp(pin.y) + '%',
                    }"
                    :aria-label="pin.city"
                >
                    <span class="mv-map-dot" aria-hidden="true"></span>

                    <span class="mv-map-card">
                        <span v-if="pin.flag_url" class="mv-map-flag">
                            <img :src="pin.flag_url" :alt="pin.city || ''" />
                        </span>
                        <span v-if="pin.city" class="mv-map-city">{{
                            pin.city
                        }}</span>
                        <span v-if="pin.label" class="mv-map-label">{{
                            pin.label
                        }}</span>
                    </span>
                </button>
            </div>
        </div>
    </section>
</template> -->
