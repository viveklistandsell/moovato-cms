<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
    Boxes,
    Building2,
    Compass,
    FileText,
    Files,
    Globe,
    Image as ImageIcon,
    Languages as LanguagesIcon,
    Loader2,
    Map,
    Menu as MenuIcon,
    Newspaper,
    Package,
    Search,
    User as UserIcon,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Dialog, DialogContent } from '@/components/ui/dialog';

type SearchItem = {
    id: number;
    title: string;
    subtitle: string;
    href: string;
};

type SearchGroup = {
    key:
        | 'pages'
        | 'posts'
        | 'media'
        | 'users'
        | 'countries'
        | 'states'
        | 'cities'
        | 'districts'
        | 'service_parents'
        | 'service_categories'
        | 'languages'
        | 'menus';
    label: string;
    items: SearchItem[];
};

const open = ref(false);
const query = ref('');
const groups = ref<SearchGroup[]>([]);
const loading = ref(false);
const cursor = ref<{ groupIdx: number; itemIdx: number }>({
    groupIdx: 0,
    itemIdx: 0,
});

let activeRequest: AbortController | null = null;
let searchTimer: ReturnType<typeof setTimeout> | null = null;

defineExpose({
    open: (): void => {
        open.value = true;
    },
    close: (): void => {
        open.value = false;
    },
    toggle: (): void => {
        open.value = !open.value;
    },
});

watch(open, (isOpen) => {
    if (!isOpen) {
        query.value = '';
        groups.value = [];
        cursor.value = { groupIdx: 0, itemIdx: 0 };
        activeRequest?.abort();
        activeRequest = null;
    } else {
        // Focus the input on the next tick so the cursor lands inside the
        // <input> instead of on the dialog wrapper.
        setTimeout(() => {
            const el = document.getElementById('admin-search-input');
            if (el instanceof HTMLInputElement) el.focus();
        }, 50);
    }
});

watch(query, () => {
    if (searchTimer !== null) clearTimeout(searchTimer);
    searchTimer = setTimeout(runSearch, 150);
});

async function runSearch(): Promise<void> {
    const term = query.value.trim();
    if (term.length < 2) {
        groups.value = [];
        loading.value = false;
        return;
    }

    // Abort any in-flight request so a fast typer never sees stale
    // results land after a newer ones.
    activeRequest?.abort();
    activeRequest = new AbortController();
    loading.value = true;

    try {
        const res = await fetch(
            `/admin/search?q=${encodeURIComponent(term)}`,
            {
                signal: activeRequest.signal,
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            },
        );
        if (!res.ok) throw new Error(`Search failed (${res.status})`);
        const json = await res.json();
        groups.value = (json.groups as SearchGroup[]) ?? [];
        cursor.value = { groupIdx: 0, itemIdx: 0 };
    } catch (e) {
        if ((e as { name?: string }).name === 'AbortError') return;
        groups.value = [];
    } finally {
        loading.value = false;
    }
}

// Group icons — picked once instead of per-render.
const GROUP_ICONS = {
    pages: Files,
    posts: Newspaper,
    media: ImageIcon,
    users: UserIcon,
    countries: Globe,
    states: Map,
    cities: Building2,
    districts: Compass,
    service_parents: Boxes,
    service_categories: Package,
    languages: LanguagesIcon,
    menus: MenuIcon,
} as const;

function iconFor(key: SearchGroup['key']): typeof FileText {
    return GROUP_ICONS[key] ?? FileText;
}

// Flatten cursor position → (groupIdx, itemIdx) into a single linear index
// so arrow-key handling is a simple increment/decrement.
const flatItems = computed<Array<{ g: number; i: number; item: SearchItem }>>(
    () => {
        const out: Array<{ g: number; i: number; item: SearchItem }> = [];
        groups.value.forEach((group, g) => {
            group.items.forEach((item, i) => {
                out.push({ g, i, item });
            });
        });
        return out;
    },
);

const flatIndex = computed<number>(() =>
    flatItems.value.findIndex(
        (entry) =>
            entry.g === cursor.value.groupIdx && entry.i === cursor.value.itemIdx,
    ),
);

function moveCursor(delta: number): void {
    if (flatItems.value.length === 0) return;
    const len = flatItems.value.length;
    const current = flatIndex.value < 0 ? 0 : flatIndex.value;
    const next = (current + delta + len) % len;
    const target = flatItems.value[next];
    cursor.value = { groupIdx: target.g, itemIdx: target.i };
}

function isHighlighted(g: number, i: number): boolean {
    return cursor.value.groupIdx === g && cursor.value.itemIdx === i;
}

function navigateTo(href: string): void {
    open.value = false;
    router.visit(href);
}

function onKeydown(e: KeyboardEvent): void {
    if (!open.value) return;
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        moveCursor(1);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        moveCursor(-1);
    } else if (e.key === 'Enter') {
        const entry = flatItems.value[flatIndex.value];
        if (entry !== undefined) {
            e.preventDefault();
            navigateTo(entry.item.href);
        }
    }
}

onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown));
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            class="top-[15%] translate-y-0 gap-0 overflow-hidden p-0 sm:max-w-2xl"
        >
            <div class="flex items-center gap-3 border-b px-4 py-3">
                <Search class="size-4 text-muted-foreground" />
                <input
                    id="admin-search-input"
                    v-model="query"
                    type="text"
                    placeholder="Search pages, posts, media, users…"
                    class="flex-1 bg-transparent text-sm outline-none placeholder:text-muted-foreground"
                    autocomplete="off"
                />
                <Loader2
                    v-if="loading"
                    class="size-4 animate-spin text-muted-foreground"
                />
                <kbd
                    v-else
                    class="rounded border bg-muted px-1.5 py-0.5 font-mono text-[10px] text-muted-foreground"
                >
                    ESC
                </kbd>
            </div>

            <div class="max-h-[60vh] overflow-y-auto p-2">
                <div
                    v-if="query.trim().length < 2"
                    class="px-4 py-8 text-center text-xs text-muted-foreground"
                >
                    Type at least 2 characters. Use
                    <kbd
                        class="mx-0.5 rounded border bg-muted px-1 py-0.5 font-mono text-[10px]"
                    >↑</kbd>
                    /
                    <kbd
                        class="mx-0.5 rounded border bg-muted px-1 py-0.5 font-mono text-[10px]"
                    >↓</kbd>
                    to navigate,
                    <kbd
                        class="mx-0.5 rounded border bg-muted px-1 py-0.5 font-mono text-[10px]"
                    >Enter</kbd>
                    to open.
                </div>

                <div
                    v-else-if="!loading && groups.length === 0"
                    class="px-4 py-8 text-center text-xs text-muted-foreground"
                >
                    No results for
                    <strong class="text-foreground">{{ query }}</strong>
                    .
                </div>

                <div
                    v-for="(group, g) in groups"
                    :key="group.key"
                    class="mb-2 last:mb-0"
                >
                    <div
                        class="flex items-center gap-1.5 px-2 pt-2 pb-1 text-[10px] font-semibold tracking-wide text-muted-foreground uppercase"
                    >
                        <component
                            :is="iconFor(group.key)"
                            class="size-3"
                        />
                        {{ group.label }}
                    </div>
                    <button
                        v-for="(item, i) in group.items"
                        :key="item.id"
                        type="button"
                        class="flex w-full items-start gap-3 rounded-md px-3 py-2 text-left transition-colors"
                        :class="
                            isHighlighted(g, i)
                                ? 'bg-accent text-accent-foreground'
                                : 'hover:bg-accent/50'
                        "
                        @click="navigateTo(item.href)"
                        @mouseenter="cursor = { groupIdx: g, itemIdx: i }"
                    >
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-medium">
                                {{ item.title }}
                            </div>
                            <div
                                class="truncate text-xs text-muted-foreground"
                            >
                                {{ item.subtitle }}
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
