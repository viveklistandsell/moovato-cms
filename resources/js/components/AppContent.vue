<script setup lang="ts">
import { computed } from 'vue';
import { SidebarInset } from '@/components/ui/sidebar';
import type { AppVariant } from '@/types';

type Props = {
    variant?: AppVariant;
    class?: string;
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});
const className = computed(() => props.class);
</script>

<template>
    <SidebarInset
        v-if="props.variant === 'sidebar'"
        :class="['overflow-hidden', className]"
    >
        <div
            class="pointer-events-none absolute inset-0 overflow-hidden"
            aria-hidden="true"
        >
            <div
                class="absolute -top-32 -left-32 size-[36rem] rounded-full bg-[var(--orange)]/22 blur-[120px]"
            />
            <div
                class="absolute top-1/4 -right-40 size-[40rem] rounded-full bg-[var(--yellow-dark)]/15 blur-[140px]"
            />
            <div
                class="absolute -bottom-40 left-1/4 size-[42rem] rounded-full bg-[var(--orange)]/12 blur-[160px]"
            />
            <div
                class="absolute -bottom-32 -right-32 size-[36rem] rounded-full bg-[var(--midnight)]/8 blur-[140px] dark:bg-[var(--orange)]/15"
            />
            <div
                class="absolute inset-x-0 bottom-0 h-72 text-[var(--midnight)] opacity-[0.10] dark:opacity-[0.18]"
                style="mask-image: linear-gradient(to top, black 30%, transparent 100%); -webkit-mask-image: linear-gradient(to top, black 30%, transparent 100%);"
            >
                <svg
                    viewBox="0 0 1920 240"
                    preserveAspectRatio="xMidYEnd slice"
                    class="size-full"
                    fill="currentColor"
                >
                    <!-- Far-left residential block -->
                    <g>
                        <rect x="0" y="200" width="60" height="40" />
                        <rect x="60" y="180" width="40" height="60" />
                        <rect x="100" y="195" width="55" height="45" />
                        <rect x="155" y="170" width="35" height="70" />
                        <rect x="190" y="190" width="70" height="50" />
                        <rect x="260" y="175" width="40" height="65" />
                        <polygon points="300,200 325,175 350,200 350,240 300,240" />
                        <rect x="350" y="195" width="50" height="45" />
                    </g>

                    <!-- Brandenburger Tor — columns + entablature + quadriga -->
                    <g transform="translate(440, 0)">
                        <rect x="0" y="180" width="14" height="60" />
                        <rect x="22" y="180" width="14" height="60" />
                        <rect x="44" y="180" width="14" height="60" />
                        <rect x="66" y="180" width="14" height="60" />
                        <rect x="88" y="180" width="14" height="60" />
                        <rect x="110" y="180" width="14" height="60" />
                        <rect x="-8" y="160" width="140" height="20" />
                        <rect x="6" y="140" width="112" height="20" />
                        <rect x="42" y="120" width="40" height="20" />
                        <polygon points="40,120 50,108 60,114 70,108 80,114 90,108 100,120" />
                        <rect x="60" y="100" width="4" height="10" />
                    </g>

                    <!-- Mid-left modern low buildings -->
                    <g>
                        <rect x="600" y="195" width="60" height="45" />
                        <rect x="660" y="180" width="40" height="60" />
                        <rect x="700" y="200" width="50" height="40" />
                    </g>

                    <!-- Reichstag — base + dome + flag pole -->
                    <g transform="translate(770, 0)">
                        <rect x="0" y="170" width="280" height="70" />
                        <rect x="-6" y="158" width="292" height="12" />
                        <rect x="6" y="140" width="20" height="18" />
                        <rect x="80" y="140" width="20" height="18" />
                        <rect x="180" y="140" width="20" height="18" />
                        <rect x="254" y="140" width="20" height="18" />
                        <ellipse cx="140" cy="140" rx="48" ry="44" />
                        <rect x="138" y="74" width="4" height="22" />
                        <polygon points="140,52 158,60 140,68" />
                    </g>

                    <!-- Potsdamer Platz-style modern towers -->
                    <g>
                        <rect x="1070" y="120" width="42" height="120" />
                        <polygon points="1070,120 1091,100 1112,120" />
                        <rect x="1120" y="95" width="40" height="145" />
                        <polygon points="1120,95 1140,75 1160,95" />
                        <rect x="1168" y="135" width="38" height="105" />
                    </g>

                    <!-- Berliner Dom — central dome + corner cupolas -->
                    <g transform="translate(1220, 0)">
                        <rect x="0" y="170" width="140" height="70" />
                        <rect x="-4" y="160" width="148" height="10" />
                        <ellipse cx="70" cy="160" rx="44" ry="48" />
                        <rect x="64" y="100" width="12" height="14" />
                        <ellipse cx="70" cy="98" rx="10" ry="8" />
                        <rect x="68" y="80" width="4" height="14" />
                        <rect x="63" y="84" width="14" height="3" />
                        <ellipse cx="14" cy="160" rx="14" ry="18" />
                        <ellipse cx="126" cy="160" rx="14" ry="18" />
                    </g>

                    <!-- Fernsehturm (TV Tower) -->
                    <g transform="translate(1390, 0)">
                        <rect x="22" y="0" width="3" height="58" />
                        <ellipse cx="23.5" cy="58" rx="6" ry="2" />
                        <circle cx="23.5" cy="78" r="20" />
                        <rect x="6" y="77" width="35" height="2" fill="var(--linen)" opacity="0.4" />
                        <polygon points="20,98 27,98 30,240 17,240" />
                        <rect x="6" y="220" width="35" height="20" />
                    </g>

                    <!-- Generic city block right of Fernsehturm -->
                    <g>
                        <rect x="1460" y="180" width="50" height="60" />
                        <rect x="1510" y="160" width="40" height="80" />
                        <rect x="1550" y="190" width="60" height="50" />
                        <rect x="1610" y="170" width="45" height="70" />
                        <rect x="1655" y="200" width="55" height="40" />
                        <rect x="1710" y="175" width="40" height="65" />
                    </g>

                    <!-- Rotes Rathaus-style clock tower -->
                    <g transform="translate(1760, 0)">
                        <rect x="0" y="180" width="80" height="60" />
                        <rect x="-4" y="170" width="88" height="10" />
                        <rect x="30" y="100" width="24" height="80" />
                        <circle cx="42" cy="130" r="6" fill="var(--linen)" opacity="0.4" />
                        <polygon points="26,100 42,80 58,100" />
                        <rect x="40" y="62" width="4" height="20" />
                    </g>

                    <!-- Far right pitched-roof closer -->
                    <g>
                        <rect x="1860" y="200" width="60" height="40" />
                    </g>
                </svg>
            </div>
        </div>

        <!-- Page content (relative so it stacks above the absolute bg). -->
        <div class="relative flex w-full flex-1 flex-col">
            <slot />
        </div>
    </SidebarInset>
    <main
        v-else
        class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl"
        :class="className"
    >
        <slot />
    </main>
</template>
