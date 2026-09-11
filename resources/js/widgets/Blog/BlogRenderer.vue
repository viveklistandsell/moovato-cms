<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ArrowRight, Image as ImageIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import { localizedUrl } from '@/lib/localizedUrl';
import NextButton from '@/widgets/shared/NextButton.vue';

type Post = {
    title?: string;
    href?: string;
    excerpt?: string | null;
    image_url?: string | null;
    created_at?: string | null;
    reading_time?: number | null;
    category?: string | null;
};

type Settings = {
    count: number;
    columns: 2 | 3 | 4;
    category: string;
    posts?: Post[];
};

type Data = {
    eyebrow?: string;
    heading?: string;
    subheading?: string;
    description?: string;
    read_more_label?: string;
    cta_label?: string;
    cta_url?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const page = usePage();
const locale = computed(
    () => (page.props.locale as string | undefined) ?? 'de',
);

const demoPosts: Post[] = [
    {
        title: 'Stressfrei umziehen: Die besten Tipps für Ihren Umzug in Berlin',
        href: '#',
        category: 'Umzugstipps',
        created_at: new Date().toISOString(),
    },
    {
        title: 'Richtig packen: So schützen Sie Ihr Hab und Gut beim Transport',
        href: '#',
        category: 'Verpackung',
        created_at: new Date().toISOString(),
    },
    {
        title: 'Büroumzug ohne Ausfallzeit: So plant Moovato Ihren Firmenumzug',
        href: '#',
        category: 'Gewerbeumzug',
        created_at: new Date().toISOString(),
    },
];

const posts = computed<Post[]>(() =>
    props.settings.posts && props.settings.posts.length > 0
        ? props.settings.posts
        : demoPosts,
);

const colsClass = computed(() => {
    switch (props.settings.columns) {
        case 2:
            return 'sm:grid-cols-2';
        case 4:
            return 'sm:grid-cols-2 lg:grid-cols-4';
        default:
            return 'sm:grid-cols-2 lg:grid-cols-3';
    }
});

const ctaHref = computed(() =>
    localizedUrl(locale.value, props.data.cta_url || '/blog'),
);

function formattedDate(value?: string | null): string {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleDateString(
        locale.value === 'de' ? 'de-DE' : 'en-US',
        { day: '2-digit', month: 'long', year: 'numeric' },
    );
}
</script>

<template>
    <section v-reveal class="mv-blog section-py">
        <div class="container-xl">
            <div>
                <span v-if="data.eyebrow" class="mv-blog-eyebrow">{{ data.eyebrow }}</span>
                <h2
                    v-if="data.heading"
                    class="mt-5 mv-section-heading"
                >
                    {{ data.heading }}
                </h2>
                <div
                    v-if="data.description"
                    class="mv-rte mt-4 text-base leading-relaxed text-[var(--slate)]"
                    v-html="data.description"
                />
                <p
                    v-if="data.subheading"
                    class="mt-4 text-base text-[var(--slate)]"
                >
                    {{ data.subheading }}
                </p>
            </div>

            <ul
                class="mt-10 grid grid-cols-1 gap-x-10 gap-y-20"
                :class="colsClass"
            >
                <li
                    v-for="(post, i) in posts"
                    :key="i"
                    class="mv-blogcard"
                >
                    <a :href="post.href || '#'" class="mv-blogcard-media">
                        <span class="mv-blogcard-frame">
                            <span class="mv-blogcard-img">
                                <img
                                    v-if="post.image_url"
                                    :src="post.image_url"
                                    :alt="post.title || ''"
                                    loading="lazy"
                                    decoding="async"
                                />
                                <ImageIcon v-else class="size-10" />
                            </span>
                        </span>
                        <span class="mv-blogcard-meta">
                            <span
                                v-if="post.category"
                                class="mv-blogcard-cat"
                                >{{ post.category }}</span
                            >
                            <span
                                v-if="post.category && post.created_at"
                                class="mv-blogcard-dot"
                            />
                            <span class="mv-blogcard-date">{{
                                formattedDate(post.created_at)
                            }}</span>
                        </span>
                    </a>

                    <a :href="post.href || '#'" class="mv-blogcard-title">
                        {{ post.title }}
                    </a>

                    <div class="mv-blogcard-foot">
                        <a :href="post.href || '#'" class="mv-blogcard-more">
                            {{ data.read_more_label || 'Mehr lesen' }}
                            <span class="mv-blogcard-more-ico">
                                <ArrowRight class="size-4" />
                            </span>
                        </a>
                    </div>
                </li>
            </ul>

            <div v-if="data.cta_label" class="mt-16 text-center">
                <NextButton :label="data.cta_label" :href="ctaHref" />
            </div>
        </div>
    </section>
</template>
