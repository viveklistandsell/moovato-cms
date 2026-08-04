<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type Category = {
    id: number;
    name: string;
    permalink: string;
    image: string | null;
    image_url: string | null;
    short_description: string | null;
    url: string;
    is_featured: boolean;
    is_popular: boolean;
};

type Settings = {
    only_featured: boolean;
    only_popular: boolean;
    max_items: number;
    columns: number;
    show_description: boolean;
    categories?: Category[];
};

type Data = {
    eyebrow?: string;
    heading?: string;
    subheading?: string;
};

const props = defineProps<{
    settings: Settings;
    data: Data;
}>();

const categories = computed<Category[]>(() => props.settings.categories ?? []);

/**
 * Map the admin-picked column count to the corresponding Tailwind grid
 * classes. Mobile stays at 2 columns for a JustDial-like phone layout,
 * scaling up as viewport widens.
 */
const gridColsClass = computed<string>(() => {
    const cols = props.settings.columns ?? 5;
    const map: Record<number, string> = {
        2: 'grid-cols-2',
        3: 'grid-cols-2 sm:grid-cols-3',
        4: 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4',
        5: 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5',
        6: 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6',
    };
    return map[cols] ?? map[5];
});
</script>

<template>
    <section v-reveal class="mv-services-grid section-py">
        <div class="container-xl">
            <header
                v-if="data.eyebrow || data.heading || data.subheading"
                class="mv-services-grid__header"
            >
                <p
                    v-if="data.eyebrow"
                    class="mv-services-grid__eyebrow"
                >
                    {{ data.eyebrow }}
                </p>
                <h2
                    v-if="data.heading"
                    class="mv-services-grid__heading"
                >
                    {{ data.heading }}
                </h2>
                <p
                    v-if="data.subheading"
                    class="mv-services-grid__subheading"
                >
                    {{ data.subheading }}
                </p>
            </header>

            <div
                v-if="categories.length > 0"
                class="mt-8 grid gap-3 sm:gap-4"
                :class="gridColsClass"
            >
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="category.url"
                    class="mv-services-grid__tile"
                >
                    <span
                        v-if="category.image_url"
                        class="mv-services-grid__image"
                    >
                        <img
                            :src="category.image_url"
                            :alt="category.name"
                            loading="lazy"
                        />
                    </span>
                    <span v-else class="mv-services-grid__icon">
                        <WidgetIcon
                            name="LayoutGrid"
                            fallback="LayoutGrid"
                            class="size-7"
                        />
                    </span>
                    <span class="mv-services-grid__name">
                        {{ category.name }}
                    </span>
                    <span
                        v-if="settings.show_description && category.short_description"
                        class="mv-services-grid__description"
                    >
                        {{ category.short_description }}
                    </span>
                </Link>
            </div>

            <p
                v-else
                class="mt-8 text-center text-sm text-[color:var(--slate)]"
            >
                Keine Kategorien gefunden.
            </p>
        </div>
    </section>
</template>
