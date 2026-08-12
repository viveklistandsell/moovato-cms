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
    label?: string;
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

const primaryCategory = computed(() => props.post.categories[0] ?? null);
</script>

<template>
    <section class="mv-blogcard-featured rounded-[32px] p-2">
        <div class="mv-blogcard-featured-inner grid gap-8 rounded-[26px] bg-white p-6 md:grid-cols-2 md:items-center md:p-10">
            <div class="space-y-4">
                <p class="inline-flex items-center rounded-full border border-[var(--orange-soft)] bg-white px-3 py-1 text-[11px] font-bold tracking-[0.18em] text-[var(--orange)] uppercase">
                    {{
                        label ??
                        (locale === 'de' ? 'Neueste Beiträge' : 'Latest Posts')
                    }}
                </p>
                <Link :href="detailHref"
                    class="mv-blogcard-title block text-2xl leading-tight font-extrabold tracking-tight hover:underline underline-offset-4 sm:text-3xl">
                    {{ post.title }}
                </Link>
                <p v-if="post.excerpt" class="line-clamp-4 text-sm leading-relaxed text-muted-foreground sm:text-base">
                    {{ post.excerpt }}
                </p>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-muted-foreground">
                    <span v-if="formattedDate" class="font-medium">{{
                        formattedDate
                        }}</span>
                    <Link v-if="primaryCategory" :href="localizedUrl(
                        locale,
                        `/blog?category=${primaryCategory.permalink}`,
                    )
                        " class="inline-flex items-center rounded-full bg-[var(--orange-soft)] px-3 py-1 text-[11px] font-bold tracking-wider text-[var(--orange)] uppercase transition-colors hover:bg-[var(--orange)] hover:text-white">
                        {{ primaryCategory.name }}
                    </Link>
                    <span v-if="post.author">· {{ post.author }}</span>
                    <span v-if="post.reading_time">· {{ post.reading_time }} min</span>
                </div>
            </div>

            <Link :href="detailHref" class="mv-blogcard-featured-media block aspect-[4/2] overflow-hidden rounded-2xl bg-muted">
                <img v-if="post.image_url" :src="post.image_url" :alt="post.title"
                    class="size-full object-cover" />
                <div v-else class="flex size-full items-center justify-center">
                    <ImageIcon class="size-12 text-muted-foreground/40" />
                </div>
            </Link>
        </div>
    </section>
</template>
