<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { ArrowRight, ChevronDown } from 'lucide-vue-next';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type SelectField = { label?: string; options?: string[] };
type ToolLink = { label?: string; url?: string };
type Column = { icon?: string; title?: string; links?: ToolLink[] };

type Settings = {
    avatar_path?: string | null;
    avatar_url?: string | null;
};

type Data = {
    assistant_title?: string;
    assistant_subtitle?: string;
    avatar_alt?: string;
    selects?: SelectField[];
    button_label?: string;
    button_url?: string;
    tools_title?: string;
    columns?: Column[];
};

defineProps<{ settings: Settings; data: Data }>();

const openIndex = ref<number | null>(null);
const selected = ref<Record<number, string>>({});
const root = ref<HTMLElement | null>(null);

function currentValue(field: SelectField, index: number): string {
    return selected.value[index] ?? field.options?.[0] ?? '';
}

function toggle(index: number): void {
    openIndex.value = openIndex.value === index ? null : index;
}

function choose(index: number, option: string): void {
    selected.value[index] = option;
    openIndex.value = null;
}

function handleOutside(event: MouseEvent): void {
    if (root.value && !root.value.contains(event.target as Node)) {
        openIndex.value = null;
    }
}

onMounted(() => {
    document.addEventListener('click', handleOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleOutside);
});
</script>

<template>
    <section ref="root" v-reveal class="mv-assistant section-py">
        <div class="container-xl">
            <div class="mv-assistant-card">
                <div class="mv-assistant-top">
                    <div
                        v-if="settings.avatar_url"
                        class="mv-assistant-avatar"
                    >
                        <img
                            :src="settings.avatar_url"
                            :alt="data.avatar_alt || ''"
                            loading="lazy"
                            decoding="async"
                        />
                    </div>
                    <div>
                        <h2
                            v-if="data.assistant_title"
                            class="mv-assistant-title"
                        >
                            {{ data.assistant_title }}
                        </h2>
                        <p
                            v-if="data.assistant_subtitle"
                            class="mv-assistant-subtitle"
                        >
                            {{ data.assistant_subtitle }}
                        </p>
                    </div>
                </div>

                <form class="mv-assistant-form" @submit.prevent>
                    <div
                        v-for="(field, i) in data.selects ?? []"
                        :key="i"
                        class="mv-assistant-select"
                        :class="{ 'is-open': openIndex === i }"
                    >
                        <span class="lbl">{{ field.label }}</span>
                        <button
                            type="button"
                            class="ctrl"
                            :aria-expanded="openIndex === i"
                            @click="toggle(i)"
                        >
                            <span class="val">{{ currentValue(field, i) }}</span>
                            <ChevronDown :size="16" />
                        </button>
                        <ul
                            v-if="openIndex === i"
                            class="mv-assistant-options"
                            role="listbox"
                        >
                            <li
                                v-for="(opt, j) in field.options ?? []"
                                :key="j"
                                role="option"
                                :aria-selected="currentValue(field, i) === opt"
                                :class="{
                                    'is-selected': currentValue(field, i) === opt,
                                }"
                                @click="choose(i, opt)"
                            >
                                {{ opt }}
                            </li>
                        </ul>
                    </div>
                    <a
                        class="mv-assistant-btn"
                        :href="data.button_url || '#'"
                    >
                        {{ data.button_label }}
                        <ArrowRight :size="16" />
                    </a>
                </form>
            </div>

            <h3 v-if="data.tools_title" class="mv-assistant-tools-title">
                {{ data.tools_title }}
            </h3>

            <div
                class="grid grid-cols-1 gap-6 lg:grid-cols-2"
            >
                <div
                    v-for="(column, i) in data.columns ?? []"
                    :key="i"
                    class="mv-assistant-col"
                >
                    <div class="mv-assistant-col-head">
                        <span class="mv-assistant-col-ico">
                            <WidgetIcon
                                :name="column.icon"
                                fallback="FileText"
                                class="size-5"
                            />
                        </span>
                        <h4 class="mv-assistant-col-title">
                            {{ column.title }}
                        </h4>
                    </div>
                    <ul class="mv-assistant-links">
                        <li
                            v-for="(link, j) in column.links ?? []"
                            :key="j"
                        >
                            <a :href="link.url || '#'">
                                {{ link.label }}
                                <ArrowRight :size="15" />
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</template>
