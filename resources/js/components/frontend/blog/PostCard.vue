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
    return props.post.categories.map((c) => c.name.toUpperCase()).join(' — ');
});
</script>

<template>
    <Link :href="detailHref" class="mv-blogcard group flex h-full flex-col rounded-[28px] p-2">
        <div class="mv-blogcard-inner flex h-full flex-col overflow-hidden rounded-[22px] bg-white">
            <div class="mv-blogcard-media relative flex aspect-[16/10] items-center justify-center overflow-hidden bg-muted">
                <img v-if="post.image_url" :src="post.image_url" :alt="post.title"
                    class="size-full object-cover" />
                <ImageIcon v-else class="size-10 text-muted-foreground/50" />
            </div>

            <div class="flex flex-1 flex-col gap-3 p-5">
                <p v-if="categoryLabel"
                    class="inline-flex w-fit items-center rounded-full bg-[var(--orange-soft)] px-3 py-1 text-[11px] font-bold tracking-wider text-[var(--orange)] uppercase">
                    {{ categoryLabel }}
                </p>
                <h3 class="mv-blogcard-title text-xl leading-tight font-extrabold text-foreground group-hover:underline underline-offset-4">
                    {{ post.title }}
                </h3>
                <p v-if="post.excerpt" class="line-clamp-3 text-sm leading-relaxed text-muted-foreground">
                    {{ post.excerpt }}
                </p>
                <div class="mt-auto flex items-center gap-2 border-t border-black/[0.06] pt-3 text-xs text-muted-foreground">
                    <span v-if="post.author">{{ post.author }}</span>
                    <span v-if="post.author && formattedDate">·</span>
                    <span v-if="formattedDate">{{ formattedDate }}</span>
                    <span v-if="post.reading_time">
                        · {{ post.reading_time }} min
                    </span>
                </div>
            </div>
        </div>
    </Link>
</template>
