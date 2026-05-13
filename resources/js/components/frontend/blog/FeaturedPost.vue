<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Image as ImageIcon } from 'lucide-vue-next';
import { computed } from 'vue';

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
    label?: string;
}>();

const detailHref = computed(
    () => `/${props.locale}/blog/${props.post.permalink}`,
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

const primaryCategory = computed(() => props.post.categories[0] ?? null);
</script>

<template>
    <section
        class="rounded-2xl border border-border/40 bg-muted/40 p-6 sm:p-8"
    >
        <div class="grid gap-8 md:grid-cols-2 md:items-center">
            <div class="space-y-4">
                <p
                    class="text-xs font-bold uppercase tracking-[0.2em] text-rose-600 dark:text-rose-400"
                >
                    {{ label ?? (locale === 'de' ? 'Neueste Beiträge' : 'Latest Posts') }}
                </p>
                <Link
                    :href="detailHref"
                    class="block text-2xl font-extrabold leading-tight tracking-tight hover:underline sm:text-3xl"
                >
                    {{ post.title }}
                </Link>
                <p
                    v-if="post.excerpt"
                    class="line-clamp-4 text-sm leading-relaxed text-muted-foreground sm:text-base"
                >
                    {{ post.excerpt }}
                </p>
                <div
                    class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-muted-foreground"
                >
                    <span v-if="formattedDate" class="font-medium">{{
                        formattedDate
                    }}</span>
                    <Link
                        v-if="primaryCategory"
                        :href="`/${locale}/blog?category=${primaryCategory.permalink}`"
                        class="text-rose-600 hover:underline dark:text-rose-400"
                    >
                        {{ primaryCategory.name }}
                    </Link>
                    <span v-if="post.author">· {{ post.author }}</span>
                    <span v-if="post.reading_time"
                        >· {{ post.reading_time }} min</span
                    >
                </div>
            </div>

            <Link
                :href="detailHref"
                class="block aspect-[4/3] overflow-hidden rounded-xl bg-muted"
            >
                <img
                    v-if="post.image_url"
                    :src="post.image_url"
                    :alt="post.title"
                    class="size-full object-cover transition-transform duration-500 hover:scale-[1.03]"
                />
                <div
                    v-else
                    class="flex size-full items-center justify-center"
                >
                    <ImageIcon class="size-12 text-muted-foreground/40" />
                </div>
            </Link>
        </div>
    </section>
</template>
