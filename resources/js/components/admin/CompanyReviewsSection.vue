<script setup lang="ts">
/**
 * Company-scoped reviews section — drops into the admin company Edit
 * page (after the FAQ card) so admins moderate + reply to reviews
 * without leaving the company they're editing.
 **/
import { router, useForm } from '@inertiajs/vue3';
import {
    Ban,
    Check,
    FileText,
    Loader2,
    MessageSquareReply,
    ShieldAlert,
    ShieldCheck,
    Star,
    ThumbsDown,
    ThumbsUp,
    Trash2,
    X,
} from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useT } from '@/composables/useT';

export type AdminReview = {
    id: number;
    public_name: string;
    is_anonymous: boolean;
    rating: number;
    body: string;
    advantages: string[];
    disadvantages: string[];
    source: string | null;
    status: 'published' | 'hidden' | 'spam';
    proof_url: string | null;
    proof_filename: string | null;
    reply_body: string | null;
    replied_at: string | null;
    reply_author_name: string | null;
    helpful_count: number;
    ip_address: string | null;
    published_at: string | null;
    created_at: string | null;
};

export type ReviewsPayload = {
    sort: string;
    data: AdminReview[];
    current_page: number;
    last_page: number;
    total: number;
    has_more: boolean;
};

const props = defineProps<{
    companyId: number;
    reviews: ReviewsPayload;
    reloadUrl: string;
}>();

const t = useT();

/* ----------------------------------- accumulator + reload logic */

const loadedReviews = ref<AdminReview[]>([...props.reviews.data]);
const currentSort = ref(props.reviews.sort);
const currentPage = ref(props.reviews.current_page);
const totalReviews = ref(props.reviews.total);
const hasMore = ref(props.reviews.has_more);
const loadingMore = ref(false);

/**
 * When the server sends a fresh `reviews` prop (partial reload)
 * merge into the accumulator:
 *   page === 1  → replace (sort changed or reply/status/delete refresh)
 *   page > 1    → append (Show more)
 */
watch(
    () => props.reviews,
    (fresh) => {
        if (fresh.current_page === 1) {
            loadedReviews.value = [...fresh.data];
        } else {
            const seen = new Set(loadedReviews.value.map((r) => r.id));
            const additions = fresh.data.filter((r) => !seen.has(r.id));
            loadedReviews.value = [...loadedReviews.value, ...additions];
        }
        currentSort.value = fresh.sort;
        currentPage.value = fresh.current_page;
        totalReviews.value = fresh.total;
        hasMore.value = fresh.has_more;
        loadingMore.value = false;
    },
);

function reload(params: Record<string, string | number>): void {
    router.get(props.reloadUrl, params, {
        only: ['reviews'],
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onFinish: () => { loadingMore.value = false; },
    });
}

function changeSort(next: string): void {
    if (next === currentSort.value) return;
    reload({ reviews_sort: next });
}

function loadMore(): void {
    if (loadingMore.value || !hasMore.value) return;
    loadingMore.value = true;
    reload({
        reviews_sort: currentSort.value,
        reviews_page: currentPage.value + 1,
    });
}

/* ----------------------------------- reply composer */

const openReplyFor = ref<number | null>(null);
const replyForms = reactive<Record<number, ReturnType<typeof useForm<{ reply_body: string }>>>>({});

function ensureReplyForm(review: AdminReview): void {
    if (replyForms[review.id]) return;
    replyForms[review.id] = useForm<{ reply_body: string }>({
        reply_body: review.reply_body ?? '',
    });
}

function toggleReply(review: AdminReview): void {
    ensureReplyForm(review);
    openReplyFor.value = openReplyFor.value === review.id ? null : review.id;
}

function submitReply(review: AdminReview): void {
    const form = replyForms[review.id];
    form.post(`/admin/reviews/${review.id}/reply`, {
        preserveScroll: true,
        onSuccess: () => { openReplyFor.value = null; },
    });
}

function deleteReply(review: AdminReview): void {
    if (!confirm(t('reviews.confirm_delete_reply'))) return;
    router.delete(`/admin/reviews/${review.id}/reply`, {
        preserveScroll: true,
        onSuccess: () => {
            openReplyFor.value = null;
            if (replyForms[review.id]) replyForms[review.id].reset();
        },
    });
}

/* ----------------------------------- status + delete */

function setStatus(review: AdminReview, status: string): void {
    router.post(
        `/admin/reviews/${review.id}/status`,
        { status },
        { preserveScroll: true },
    );
}

function deleteReview(review: AdminReview): void {
    if (!confirm(t('reviews.confirm_delete_review'))) return;
    router.delete(`/admin/reviews/${review.id}`, { preserveScroll: true });
}

/* ----------------------------------- formatting */

function formatDate(iso: string | null): string {
    if (!iso) return '—';
    try {
        return new Date(iso).toLocaleDateString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric',
        });
    } catch {
        return iso;
    }
}

function tagLabel(tag: string): string {
    return t(`reviews.tags.${tag}`, { fallback: tag });
}

function tagLabelNegative(tag: string): string {
    return t(`reviews.tags_negative.${tag}`, { fallback: tag });
}

/* ----------------------------------- summary */

const publishedCount = computed(() =>
    loadedReviews.value.filter((r) => r.status === 'published').length,
);
const awaitingReplyCount = computed(() =>
    loadedReviews.value.filter((r) => r.status === 'published' && !r.reply_body).length,
);
const sortOptions = ['newest', 'oldest', 'rating_high', 'rating_low'];
</script>

<template>
    <Card>
        <CardHeader class="flex flex-row items-start justify-between gap-3 space-y-0">
            <div class="min-w-0">
                <CardTitle class="flex flex-wrap items-center gap-2">
                    {{ t('reviews.reviews_title') }}
                    <Badge variant="secondary" class="text-xs">
                        {{ totalReviews }}
                    </Badge>
                    <Badge
                        v-if="awaitingReplyCount > 0"
                        class="bg-orange-100 text-[10px] font-semibold text-[var(--orange)] dark:bg-orange-950/40"
                    >
                        {{ awaitingReplyCount }} {{ t('reviews.awaiting_short') }}
                    </Badge>
                </CardTitle>
                <CardDescription>
                    {{ t('reviews.reviews_description') }}
                </CardDescription>
            </div>
            <div v-if="totalReviews > 1" class="shrink-0">
                <Select
                    :model-value="currentSort"
                    @update:model-value="(v) => changeSort(String(v))"
                >
                    <SelectTrigger class="h-8 min-w-[160px] text-xs">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="s in sortOptions"
                            :key="s"
                            :value="s"
                        >
                            {{ t(`reviews.sort.${s}`) }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </CardHeader>

        <CardContent class="p-0">
            <div v-if="totalReviews === 0" class="p-6 text-center text-sm text-muted-foreground">
                {{ t('reviews.no_reviews') }}
            </div>

            <ul v-else class="divide-y divide-border">
                <li
                    v-for="review in loadedReviews"
                    :key="review.id"
                    class="p-4 md:p-5"
                >
                    <div class="mb-3 flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                                <span class="text-sm font-medium text-foreground">
                                    {{ review.public_name }}
                                </span>
                                <span v-if="review.is_anonymous" class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] dark:bg-slate-800">
                                    {{ t('reviews.anonymous') }}
                                </span>
                                <span>·</span>
                                <span>{{ formatDate(review.published_at || review.created_at) }}</span>
                                <span v-if="review.source">·</span>
                                <span v-if="review.source" class="capitalize">
                                    {{ t(`reviews.sources.${review.source}`, { fallback: review.source }) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-0.5">
                                <Star
                                    v-for="n in 5"
                                    :key="n"
                                    class="size-4"
                                    :class="n <= review.rating
                                        ? 'fill-amber-400 text-amber-400'
                                        : 'text-slate-300'"
                                />
                            </div>
                            <Badge
                                :variant="review.status === 'published' ? 'default' : 'secondary'"
                                :class="review.status === 'spam'
                                    ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300'
                                    : review.status === 'hidden'
                                        ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                        : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'"
                            >
                                {{ t(`reviews.status.${review.status}`) }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Body -->
                    <p class="mb-2 whitespace-pre-line text-sm leading-relaxed">
                        {{ review.body }}
                    </p>

                    <!-- Tags -->
                    <div
                        v-if="review.advantages.length || review.disadvantages.length"
                        class="mb-3 flex flex-wrap gap-1.5"
                    >
                        <span
                            v-for="tag in review.advantages"
                            :key="`adv-${review.id}-${tag}`"
                            class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300"
                        >
                            <ThumbsUp class="size-3" />
                            {{ tagLabel(tag) }}
                        </span>
                        <span
                            v-for="tag in review.disadvantages"
                            :key="`dis-${review.id}-${tag}`"
                            class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-2 py-0.5 text-[11px] text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300"
                        >
                            <ThumbsDown class="size-3" />
                            {{ tagLabelNegative(tag) }}
                        </span>
                    </div>

                    <!-- Existing reply (collapsed to preview mode) -->
                    <div
                        v-if="review.reply_body && openReplyFor !== review.id"
                        class="mb-3 rounded-md border border-[var(--linen)] bg-[var(--paper)] p-3 dark:border-slate-800 dark:bg-slate-900/40"
                    >
                        <div class="mb-1 flex flex-wrap items-center gap-1.5 text-xs font-semibold text-[var(--midnight)] dark:text-white">
                            <MessageSquareReply class="size-3.5 text-[var(--orange)]" />
                            {{ t('reviews.reply_by_company') }}
                            <span v-if="review.reply_author_name" class="font-normal text-muted-foreground">
                                · {{ review.reply_author_name }}
                            </span>
                            <span v-if="review.replied_at" class="font-normal text-muted-foreground">
                                · {{ formatDate(review.replied_at) }}
                            </span>
                        </div>
                        <p class="whitespace-pre-line text-sm">{{ review.reply_body }}</p>
                    </div>

                    <!-- Reply composer -->
                    <div
                        v-if="openReplyFor === review.id"
                        class="mb-3 rounded-md border border-[var(--orange)] bg-[var(--orange-soft)]/30 p-3"
                    >
                        <label class="mb-1 block text-xs font-semibold text-[var(--midnight)] dark:text-white">
                            {{ review.reply_body ? t('reviews.edit_reply') : t('reviews.write_reply') }}
                        </label>
                        <textarea
                            v-model="replyForms[review.id].reply_body"
                            rows="3"
                            :placeholder="t('reviews.reply_placeholder')"
                            class="w-full rounded-md border border-[var(--linen)] bg-white p-2 text-sm focus:border-[var(--orange)] focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                        />
                        <p v-if="replyForms[review.id].errors.reply_body" class="mt-1 text-xs text-red-600">
                            {{ replyForms[review.id].errors.reply_body }}
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <Button
                                size="sm"
                                :disabled="replyForms[review.id].processing"
                                type="button"
                                @click="submitReply(review)"
                            >
                                <Check class="size-4" />
                                {{ t('reviews.save_reply') }}
                            </Button>
                            <Button size="sm" variant="ghost" type="button" @click="openReplyFor = null">
                                <X class="size-4" />
                                {{ t('reviews.cancel') }}
                            </Button>
                            <Button
                                v-if="review.reply_body"
                                size="sm"
                                variant="ghost"
                                type="button"
                                class="text-red-600 hover:text-red-700"
                                @click="deleteReply(review)"
                            >
                                <Trash2 class="size-4" />
                                {{ t('reviews.delete_reply') }}
                            </Button>
                        </div>
                    </div>

                    <!-- Action row -->
                    <div class="flex flex-wrap items-center gap-2">
                        <Button
                            size="sm"
                            variant="outline"
                            type="button"
                            @click="toggleReply(review)"
                        >
                            <MessageSquareReply class="size-4" />
                            {{ review.reply_body ? t('reviews.edit_reply') : t('reviews.write_reply') }}
                        </Button>

                        <a
                            v-if="review.proof_url"
                            :href="review.proof_url"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-1.5 rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium text-[var(--orange)] hover:bg-[var(--orange-soft)] hover:text-[var(--orange)]"
                            :title="review.proof_filename ?? ''"
                        >
                            <FileText class="size-4" />
                            {{ t('reviews.view_proof') }}
                        </a>

                        <Button
                            v-if="review.status !== 'published'"
                            size="sm"
                            variant="outline"
                            type="button"
                            @click="setStatus(review, 'published')"
                        >
                            <ShieldCheck class="size-4" />
                            {{ t('reviews.publish') }}
                        </Button>
                        <Button
                            v-if="review.status !== 'hidden'"
                            size="sm"
                            variant="outline"
                            type="button"
                            @click="setStatus(review, 'hidden')"
                        >
                            <Ban class="size-4" />
                            {{ t('reviews.hide') }}
                        </Button>
                        <Button
                            v-if="review.status !== 'spam'"
                            size="sm"
                            variant="outline"
                            type="button"
                            class="text-red-600 hover:text-red-700"
                            @click="setStatus(review, 'spam')"
                        >
                            <ShieldAlert class="size-4" />
                            {{ t('reviews.mark_spam') }}
                        </Button>

                        <Button
                            size="sm"
                            variant="ghost"
                            type="button"
                            class="text-red-600 hover:text-red-700"
                            @click="deleteReview(review)"
                        >
                            <Trash2 class="size-4" />
                            {{ t('reviews.delete') }}
                        </Button>

                        <span v-if="review.ip_address" class="ml-auto text-[10px] text-muted-foreground">
                            IP: {{ review.ip_address }}
                        </span>
                    </div>
                </li>
            </ul>

            <!-- Show more -->
            <div v-if="hasMore" class="border-t border-border p-4 text-center">
                <Button
                    variant="outline"
                    :disabled="loadingMore"
                    type="button"
                    @click="loadMore"
                >
                    <Loader2 v-if="loadingMore" class="size-4 animate-spin" />
                    {{ t('reviews.show_more_admin') }}
                </Button>
                <p class="mt-1 text-xs text-muted-foreground">
                    {{ loadedReviews.length }} / {{ totalReviews }}
                </p>
            </div>
        </CardContent>
    </Card>
</template>
