<script setup lang="ts">
import { computed, useId } from 'vue';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type Stat = { value?: string; line1?: string; line2?: string };

type Settings = {
    theme: 'dark' | 'light';
    show_badge: boolean;
};

type Data = {
    stats?: Stat[];
    badge_text?: string;
    badge_icon?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

// Unique per instance so multiple Trust Bars on one page don't share an id.
const pathId = `trust-badge-path-${useId()}`;

const stats = computed(() => props.data.stats ?? []);
</script>

<template>
    <section
        class="trust-bar section-py"
        :class="`trust-bar--${settings.theme}`"
    >
        <div class="container-xl flex flex-wrap items-center gap-9 md:gap-14">
            <dl
                v-if="stats.length"
                class="m-0 flex flex-1 flex-wrap gap-10 md:gap-18"
            >
                <div v-for="(stat, i) in stats" :key="i" class="trust-stat">
                    <dt class="trust-stat__value">{{ stat.value }}</dt>
                    <dd class="trust-stat__text">
                        <span v-if="stat.line1">{{ stat.line1 }}</span>
                        <span v-if="stat.line2">{{ stat.line2 }}</span>
                    </dd>
                </div>
            </dl>

            <div
                v-if="settings.show_badge && data.badge_text"
                class="trust-badge"
            >
                <svg
                    class="trust-badge__ring"
                    viewBox="0 0 200 200"
                    aria-hidden="true"
                >
                    <defs>
                        <path
                            :id="pathId"
                            d="M 100,100 m -78,0 a 78,78 0 1,1 156,0 a 78,78 0 1,1 -156,0"
                            fill="none"
                        />
                    </defs>
                    <text>
                        <textPath :href="`#${pathId}`" startOffset="0">
                            {{ data.badge_text }}
                        </textPath>
                    </text>
                </svg>
                <span class="trust-badge__arrow">
                    <WidgetIcon
                        :name="data.badge_icon"
                        fallback="ArrowUpRight"
                        class="size-9"
                    />
                </span>
            </div>
        </div>
    </section>
</template>
