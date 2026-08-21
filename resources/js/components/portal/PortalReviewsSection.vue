<script setup lang="ts">

import { router } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    Ban,
    Check,
    Eye,
    EyeOff,
    Loader2,
    Lock,
    MessageCircleReply,
    ShieldAlert,
    Star,
    Trash2,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Review = {
    id: number;
    author_name: string | null;
    author_initials: string | null;
    is_anonymous: boolean;
    public_name: string;
    rating: number;
    body: string;
    advantages: string[];
    disadvantages: string[];
    status: 'published' | 'hidden' | 'spam';
    reply_body: string | null;
    replied_at: string | null;
    helpful_count: number;
    created_at: string | null;
    published_at: string | null;
};

type ReviewsFeed = {
    data: Review[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    has_more: boolean;
    reply_cap: number | null; // null = unlimited
    reply_used: number;
    reply_remaining: number | null; // null = unlimited
    can_reply_new: boolean;
    plans_url: string;
};

const props = defineProps<{
    reviews?: ReviewsFeed;
    lazyLoaded: boolean;
    locale: string;
}>();

const emit = defineEmits<{
    (e: 'lazyLoad'): void;
    (e: 'refresh'): void;
}>();

const de = {
    title: 'Bewertungen',
    subtitle: 'Kundenbewertungen ansehen, im Namen des Unternehmens antworten und beleidigende Inhalte moderieren.',
    loading: 'Bewertungen werden geladen…',
    empty: 'Noch keine Bewertungen erhalten.',
    reply: 'Antworten',
    edit_reply: 'Antwort bearbeiten',
    remove_reply: 'Antwort entfernen',
    save_reply: 'Antwort speichern',
    cancel: 'Abbrechen',
    reply_placeholder: 'Schreiben Sie eine freundliche, professionelle Antwort…',
    hide: 'Ausblenden',
    unhide: 'Wieder anzeigen',
    mark_spam: 'Als Spam markieren',
    unmark_spam: 'Spam-Markierung entfernen',
    delete: 'Löschen',
    confirm_remove_reply: 'Antwort wirklich entfernen?',
    confirm_delete: 'Diese Bewertung wirklich löschen? Dies kann nicht rückgängig gemacht werden.',
    replied_at: 'Antwort vom Unternehmen',
    cap_used: '{used} von {cap} Antworten genutzt',
    cap_unlimited: 'Unbegrenzte Antworten',
    cap_reached: 'Antwort-Limit erreicht',
    cap_reached_body: 'Sie haben Ihre {cap} kostenlosen Antworten aufgebraucht. Upgraden Sie Ihren Plan, um weiter zu antworten.',
    upgrade_plan: 'Plan upgraden',
    status_hidden: 'Ausgeblendet',
    status_spam: 'Spam',
    status_published: 'Veröffentlicht',
    anonymous: 'Anonym',
    load_more: 'Weitere Bewertungen laden',
    replies_awaiting: '{n} wartet auf Antwort|{n} warten auf Antwort',
} as const;
const en = {
    title: 'Reviews',
    subtitle: 'Read customer reviews, reply on behalf of the company, and moderate abusive content.',
    loading: 'Loading reviews…',
    empty: 'No reviews received yet.',
    reply: 'Reply',
    edit_reply: 'Edit reply',
    remove_reply: 'Remove reply',
    save_reply: 'Save reply',
    cancel: 'Cancel',
    reply_placeholder: 'Write a friendly, professional reply…',
    hide: 'Hide',
    unhide: 'Unhide',
    mark_spam: 'Mark spam',
    unmark_spam: 'Unmark spam',
    delete: 'Delete',
    confirm_remove_reply: 'Really remove this reply?',
    confirm_delete: 'Really delete this review? This cannot be undone.',
    replied_at: 'Company reply',
    cap_used: '{used} of {cap} replies used',
    cap_unlimited: 'Unlimited replies',
    cap_reached: 'Reply limit reached',
    cap_reached_body: 'You\'ve used all {cap} free replies. Upgrade your plan to keep replying.',
    upgrade_plan: 'Upgrade plan',
    status_hidden: 'Hidden',
    status_spam: 'Spam',
    status_published: 'Published',
    anonymous: 'Anonymous',
    load_more: 'Load more reviews',
    replies_awaiting: '{n} awaiting reply|{n} awaiting reply',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

function interp(msg: string, vars: Record<string, string | number>): string {
    return msg.replace(/\{(\w+)\}/g, (_, k) => String(vars[k] ?? ''));
}

/* -------------------- lazy-load trigger -------------------- */
if (!props.lazyLoaded) {
    emit('lazyLoad');
}

/* -------------------- reply editor state -------------------- */

// Per-review draft text — keyed by review id so multiple reply
// editors can be open at once without stomping on each other.
const editingReplyId = ref<number | null>(null);
const draftText = ref('');
const busyId = ref<number | null>(null);

function openReplyEditor(r: Review): void {
    editingReplyId.value = r.id;
    draftText.value = r.reply_body ?? '';
}

function cancelReplyEditor(): void {
    editingReplyId.value = null;
    draftText.value = '';
}

function submitReply(r: Review): void {
    if (draftText.value.trim() === '') return;
    busyId.value = r.id;
    router.post(
        `/partner/reviews/${r.id}/reply`,
        { reply_body: draftText.value.trim() },
        {
            preserveScroll: true,
            onFinish: () => {
                busyId.value = null;
            },
            onSuccess: () => {
                cancelReplyEditor();
                emit('refresh');
            },
        },
    );
}

function removeReply(r: Review): void {
    if (!confirm(t.value.confirm_remove_reply)) return;
    busyId.value = r.id;
    router.delete(`/partner/reviews/${r.id}/reply`, {
        preserveScroll: true,
        onFinish: () => {
            busyId.value = null;
        },
        onSuccess: () => emit('refresh'),
    });
}

/* -------------------- moderation actions -------------------- */

function hide(r: Review): void {
    busyId.value = r.id;
    router.post(`/partner/reviews/${r.id}/hide`, {}, {
        preserveScroll: true,
        onFinish: () => { busyId.value = null; },
        onSuccess: () => emit('refresh'),
    });
}

function unhide(r: Review): void {
    busyId.value = r.id;
    router.post(`/partner/reviews/${r.id}/unhide`, {}, {
        preserveScroll: true,
        onFinish: () => { busyId.value = null; },
        onSuccess: () => emit('refresh'),
    });
}

function markSpam(r: Review): void {
    busyId.value = r.id;
    router.post(`/partner/reviews/${r.id}/mark-spam`, {}, {
        preserveScroll: true,
        onFinish: () => { busyId.value = null; },
        onSuccess: () => emit('refresh'),
    });
}

function unmarkSpam(r: Review): void {
    busyId.value = r.id;
    router.post(`/partner/reviews/${r.id}/unmark-spam`, {}, {
        preserveScroll: true,
        onFinish: () => { busyId.value = null; },
        onSuccess: () => emit('refresh'),
    });
}

function deleteReview(r: Review): void {
    if (!confirm(t.value.confirm_delete)) return;
    busyId.value = r.id;
    router.delete(`/partner/reviews/${r.id}`, {
        preserveScroll: true,
        onFinish: () => { busyId.value = null; },
        onSuccess: () => emit('refresh'),
    });
}

/* -------------------- helpers -------------------- */

function formatDate(iso: string | null): string {
    if (!iso) return '';
    try {
        return new Date(iso).toLocaleDateString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric',
        });
    } catch {
        return iso;
    }
}

function statusLabel(status: string): string {
    switch (status) {
        case 'hidden': return t.value.status_hidden;
        case 'spam': return t.value.status_spam;
        default: return t.value.status_published;
    }
}

const capReached = computed(() => {
    if (!props.reviews) return false;
    return props.reviews.can_reply_new === false;
});

const awaitingReplyCount = computed(() => {
    if (!props.reviews) return 0;
    return props.reviews.data.filter((r) => r.reply_body === null || r.reply_body === '').length;
});

function loadMore(): void {
    if (!props.reviews || !props.reviews.has_more) return;
    const next = props.reviews.current_page + 1;
    router.reload({
        only: ['reviews'],
        data: { reviews_page: next },
    });
}
</script>

<template>
    <section class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-5 shadow-sm sm:p-6">
        <header class="mb-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="flex items-center gap-2 text-lg font-bold text-[var(--midnight)]">
                    <MessageCircleReply class="size-5 text-[var(--orange)]" />
                    {{ t.title }}
                    <span
                        v-if="reviews && reviews.total > 0"
                        class="rounded-full bg-[var(--paper)] px-2 py-0.5 text-xs font-semibold text-[var(--slate)]"
                    >
                        {{ reviews.total }}
                    </span>
                    <span
                        v-if="awaitingReplyCount > 0"
                        class="rounded-full border border-amber-300 bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-800"
                    >
                        {{ interp(awaitingReplyCount === 1 ? t.replies_awaiting.split('|')[0] : t.replies_awaiting.split('|')[1], { n: awaitingReplyCount }) }}
                    </span>
                </p>
                <p class="mt-1 text-xs text-[var(--slate)]">{{ t.subtitle }}</p>
            </div>

            <!-- Cap indicator — always visible so partners can plan. -->
            <div
                v-if="reviews"
                class="rounded-lg border border-[var(--linen)] bg-[var(--paper)] px-3 py-2 text-right"
            >
                <p
                    v-if="reviews.reply_cap === null"
                    class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700"
                >
                    <Check class="size-3.5" />
                    {{ t.cap_unlimited }}
                </p>
                <p v-else class="text-[11px] font-medium text-[var(--slate)]">
                    {{ interp(t.cap_used, { used: reviews.reply_used, cap: reviews.reply_cap }) }}
                </p>
            </div>
        </header>

        <!-- Cap-reached banner — inline, so it's impossible to miss -->
        <div
            v-if="capReached && reviews"
            class="mb-4 flex flex-wrap items-start justify-between gap-3 rounded-lg border border-amber-300 bg-amber-50 p-3"
        >
            <div class="flex items-start gap-2">
                <Lock class="mt-0.5 size-4 shrink-0 text-amber-700" />
                <div>
                    <p class="text-sm font-semibold text-amber-900">{{ t.cap_reached }}</p>
                    <p class="mt-0.5 text-xs text-amber-800">
                        {{ interp(t.cap_reached_body, { cap: String(reviews.reply_cap ?? '') }) }}
                    </p>
                </div>
            </div>
            <a
                :href="reviews.plans_url"
                class="inline-flex items-center gap-1.5 rounded-lg bg-[var(--orange)] px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)]"
            >
                <ArrowUpRight class="size-3.5" />
                {{ t.upgrade_plan }}
            </a>
        </div>

        <!-- Loading state -->
        <div
            v-if="!lazyLoaded || !reviews"
            class="flex items-center justify-center gap-2 rounded-md border border-dashed border-[var(--linen)] p-8 text-sm text-[var(--slate)]"
        >
            <Loader2 class="size-4 animate-spin" />
            {{ t.loading }}
        </div>

        <!-- Empty state -->
        <div
            v-else-if="reviews.data.length === 0"
            class="rounded-md border border-dashed border-[var(--linen)] p-8 text-center text-sm text-[var(--slate)]"
        >
            {{ t.empty }}
        </div>

        <!-- Review list -->
        <ul v-else class="space-y-4">
            <li
                v-for="r in reviews.data"
                :key="r.id"
                class="rounded-lg border border-[var(--linen)] bg-[var(--paper)]/40 p-4"
                :class="{ 'opacity-60': r.status !== 'published' }"
            >
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-full bg-[var(--orange-soft)] text-xs font-semibold text-[var(--orange)]">
                            {{ r.author_initials || '?' }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--midnight)]">
                                {{ r.is_anonymous ? t.anonymous : r.public_name }}
                            </p>
                            <p class="text-[11px] text-[var(--slate)]">
                                {{ formatDate(r.published_at ?? r.created_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-0.5">
                            <Star
                                v-for="i in 5"
                                :key="i"
                                class="size-3.5"
                                :class="i <= r.rating ? 'fill-amber-400 text-amber-500' : 'text-slate-300'"
                            />
                        </div>
                        <span
                            v-if="r.status !== 'published'"
                            class="rounded-full border border-slate-300 bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-slate-700"
                        >
                            {{ statusLabel(r.status) }}
                        </span>
                    </div>
                </div>

                <p class="mt-3 text-sm leading-relaxed text-[var(--midnight)] whitespace-pre-wrap">
                    {{ r.body }}
                </p>

                <!-- Existing reply -->
                <div
                    v-if="r.reply_body && editingReplyId !== r.id"
                    class="mt-3 rounded-md border-l-2 border-[var(--orange)] bg-[var(--white)] p-3"
                >
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-wider text-[var(--orange)]">
                        {{ t.replied_at }} · {{ formatDate(r.replied_at) }}
                    </p>
                    <p class="text-xs leading-relaxed text-[var(--slate)] whitespace-pre-wrap">
                        {{ r.reply_body }}
                    </p>
                </div>

                <!-- Reply editor -->
                <div v-if="editingReplyId === r.id" class="mt-3 space-y-2">
                    <textarea
                        v-model="draftText"
                        rows="3"
                        :placeholder="t.reply_placeholder"
                        class="w-full rounded-md border border-[var(--linen)] bg-white px-3 py-2 text-sm text-[var(--midnight)] focus:border-[var(--orange)] focus:outline-none focus:ring-1 focus:ring-[var(--orange)]"
                    ></textarea>
                    <div class="flex items-center justify-end gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 rounded-md border border-[var(--linen)] px-3 py-1.5 text-xs font-medium text-[var(--slate)] hover:border-[var(--slate-light)]"
                            @click="cancelReplyEditor"
                        >
                            <X class="size-3.5" />
                            {{ t.cancel }}
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-md bg-[var(--orange)] px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)] disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="busyId === r.id || draftText.trim() === ''"
                            @click="submitReply(r)"
                        >
                            <Loader2 v-if="busyId === r.id" class="size-3.5 animate-spin" />
                            <Check v-else class="size-3.5" />
                            {{ t.save_reply }}
                        </button>
                    </div>
                </div>

                <!-- Action row -->
                <div v-if="editingReplyId !== r.id" class="mt-3 flex flex-wrap items-center gap-1.5">
                    <!-- Reply / Edit reply — cap-gated ONLY for new replies -->
                    <template v-if="r.reply_body === null">
                        <button
                            v-if="reviews && (reviews.can_reply_new || r.reply_body !== null)"
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-md border border-emerald-300 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 transition-colors hover:bg-emerald-100"
                            @click="openReplyEditor(r)"
                        >
                            <MessageCircleReply class="size-3.5" />
                            {{ t.reply }}
                        </button>
                        <a
                            v-else-if="reviews"
                            :href="reviews.plans_url"
                            class="inline-flex items-center gap-1.5 rounded-md border border-amber-300 bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-800 transition-colors hover:bg-amber-100"
                        >
                            <Lock class="size-3.5" />
                            {{ t.upgrade_plan }}
                        </a>
                    </template>
                    <template v-else>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-md border border-[var(--linen)] bg-[var(--white)] px-2.5 py-1 text-[11px] font-semibold text-[var(--slate)] transition-colors hover:border-[var(--orange)] hover:text-[var(--orange)]"
                            @click="openReplyEditor(r)"
                        >
                            <MessageCircleReply class="size-3.5" />
                            {{ t.edit_reply }}
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-md border border-[var(--linen)] bg-[var(--white)] px-2.5 py-1 text-[11px] font-semibold text-[var(--slate)] transition-colors hover:border-rose-300 hover:text-rose-700"
                            @click="removeReply(r)"
                        >
                            <X class="size-3.5" />
                            {{ t.remove_reply }}
                        </button>
                    </template>

                    <!-- Moderation actions -->
                    <button
                        v-if="r.status === 'published'"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-md border border-[var(--linen)] bg-[var(--white)] px-2.5 py-1 text-[11px] font-semibold text-[var(--slate)] transition-colors hover:border-slate-400 hover:text-slate-900"
                        @click="hide(r)"
                    >
                        <EyeOff class="size-3.5" />
                        {{ t.hide }}
                    </button>
                    <button
                        v-else-if="r.status === 'hidden'"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-md border border-[var(--linen)] bg-[var(--white)] px-2.5 py-1 text-[11px] font-semibold text-[var(--slate)] transition-colors hover:border-emerald-300 hover:text-emerald-700"
                        @click="unhide(r)"
                    >
                        <Eye class="size-3.5" />
                        {{ t.unhide }}
                    </button>

                    <button
                        v-if="r.status !== 'spam'"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-md border border-[var(--linen)] bg-[var(--white)] px-2.5 py-1 text-[11px] font-semibold text-[var(--slate)] transition-colors hover:border-rose-300 hover:text-rose-700"
                        @click="markSpam(r)"
                    >
                        <ShieldAlert class="size-3.5" />
                        {{ t.mark_spam }}
                    </button>
                    <button
                        v-else
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-md border border-[var(--linen)] bg-[var(--white)] px-2.5 py-1 text-[11px] font-semibold text-[var(--slate)] transition-colors hover:border-emerald-300 hover:text-emerald-700"
                        @click="unmarkSpam(r)"
                    >
                        <Ban class="size-3.5" />
                        {{ t.unmark_spam }}
                    </button>

                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-md border border-[var(--linen)] bg-[var(--white)] px-2.5 py-1 text-[11px] font-semibold text-rose-700 transition-colors hover:border-rose-400 hover:bg-rose-50"
                        @click="deleteReview(r)"
                    >
                        <Trash2 class="size-3.5" />
                        {{ t.delete }}
                    </button>
                </div>
            </li>
        </ul>

        <!-- Load-more pagination -->
        <div v-if="reviews && reviews.has_more" class="mt-4 flex justify-center">
            <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-md border border-[var(--linen)] bg-[var(--white)] px-4 py-2 text-xs font-semibold text-[var(--slate)] hover:border-[var(--orange)] hover:text-[var(--orange)]"
                @click="loadMore"
            >
                {{ t.load_more }}
            </button>
        </div>
    </section>
</template>
