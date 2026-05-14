<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Image as ImageIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import { localizedUrl } from '@/lib/localizedUrl';

type Category = { id: number; name: string; permalink: string };

type Post = {
    id: number;
    title: string;
    permalink: string;
    excerpt: string | null;
    image_url: string | null;
    author: string | null;
    created_at: string | null;
    reading_time: number;
    categories: Category[];
};

const props = defineProps<{
    post: Post;
    locale: string;
}>();

const detailHref = computed(() =>
    localizedUrl(props.locale, `/blog/${props.post.permalink}`),
);

const formattedDate = computed(() => {
    if (!props.post.created_at) {
        return '';
    }
    return new Date(props.post.created_at).toLocaleDateString(
        props.locale === 'de' ? 'de-DE' : 'en-US',
        { day: '2-digit', month: 'short', year: 'numeric' },
    );
});

const categoryLabel = computed(() => {
    if (props.post.categories.length === 0) {
        return null;
    }
    return props.post.categories
        .map((c) => c.name.toUpperCase())
        .join(' — ');
});
</script>

<template>
    <Link
        :href="detailHref"
        class="group flex flex-col overflow-hidden rounded-lg border border-transparent transition-all hover:border-border"
    >
        <div
            class="flex aspect-[16/10] items-center justify-center overflow-hidden rounded-lg bg-muted"
        >
            <img
                v-if="post.image_url"
                :src="post.image_url"
                :alt="post.title"
                class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
            />
            <ImageIcon v-else class="size-10 text-muted-foreground/50" />
        </div>

        <div class="flex flex-col gap-2 px-1 pt-4">
            <p
                v-if="categoryLabel"
                class="text-xs font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400"
            >
                {{ categoryLabel }}
            </p>
            <h3
                class="text-lg font-bold leading-tight text-foreground group-hover:underline"
            >
                {{ post.title }}
            </h3>
            <p
                v-if="post.excerpt"
                class="line-clamp-3 text-sm text-muted-foreground"
            >
                {{ post.excerpt }}
            </p>
            <div
                class="mt-2 flex items-center gap-2 text-xs text-muted-foreground"
            >
                <span v-if="post.author">{{ post.author }}</span>
                <span v-if="post.author && formattedDate">·</span>
                <span v-if="formattedDate">{{ formattedDate }}</span>
                <span v-if="post.reading_time">
                    · {{ post.reading_time }} min
                </span>
            </div>
        </div>
    </Link>
</template>
