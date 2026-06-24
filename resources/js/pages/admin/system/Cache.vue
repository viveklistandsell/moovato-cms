<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    AlertTriangle,
    Database,
    Loader2,
    RefreshCw,
    Trash2,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useAdminLocale } from '@/composables/useAdminLocale';
import { useT } from '@/composables/useT';

const t = useT();
const adminLocale = useAdminLocale();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.system'), href: '/admin/system/cache' },
    { title: t('sidebar.cache_management'), href: '/admin/system/cache' },
]);

type Tone = 'rose' | 'sky' | 'violet';

type CacheSection = {
    type: string;
    title: string;
    description: string;
    tone: Tone;
};

const props = defineProps<{
    sections: CacheSection[];
}>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

// Last-cleared timestamps purely for UX feedback inside this session.
const clearedAt = ref<Record<string, string>>({});
const pending = ref<string | null>(null);

const page = usePage();
const flash = computed<{ type?: string; message?: string } | null>(() => {
    const toast = (page.props as Record<string, unknown>).toast;
    return (toast ?? null) as { type?: string; message?: string } | null;
});

function clear(type: string): void {
    if (pending.value) return;

    const isAll = type === 'all';
    if (isAll) {
        const ok = confirm(t('system.cache_confirm_flush_all'));
        if (!ok) return;
    }

    pending.value = type;
    router.post(
        '/admin/system/cache/clear',
        { type },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                clearedAt.value = {
                    ...clearedAt.value,
                    [type]: new Date().toLocaleTimeString(adminLocale.value),
                };
            },
            onFinish: () => {
                pending.value = null;
            },
        },
    );
}

// Group sections by visual tone so the screen reads as three deliberate
// blocks (danger zone → framework caches → app caches) instead of one long
// uniform list.
const grouped = computed(() => {
    const out: Record<Tone, CacheSection[]> = { rose: [], sky: [], violet: [] };
    for (const s of props.sections) out[s.tone].push(s);
    return out;
});

function toneClass(tone: Tone, kind: 'badge' | 'button'): string {
    const map: Record<Tone, { badge: string; button: string }> = {
        rose: {
            badge: 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
            button: 'bg-rose-600 hover:bg-rose-700 text-white',
        },
        sky: {
            badge: 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
            button: '',
        },
        violet: {
            badge: 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300',
            button: '',
        },
    };
    return map[tone][kind];
}
</script>

<template>
    <Head :title="t('system.cache_title')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="t('system.cache_title')"
            :description="t('system.cache_description')"
        />

        <div
            v-if="flash?.message"
            class="rounded-md border px-4 py-3 text-sm"
            :class="flash.type === 'error'
                ? 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-200'
                : 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200'"
        >
            {{ flash.message }}
        </div>

        <!-- Everything (rose / danger zone) -->
        <div v-if="grouped.rose.length > 0" class="space-y-3">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                <AlertTriangle class="size-4" />
                {{ t('system.cache_danger_zone') }}
            </div>
            <Card v-for="s in grouped.rose" :key="s.type" class="border-rose-200/60 dark:border-rose-900/40">
                <CardHeader class="flex flex-row items-start justify-between gap-4">
                    <div class="space-y-1">
                        <CardTitle class="flex items-center gap-2">
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase" :class="toneClass(s.tone, 'badge')">{{ s.type }}</span>
                            {{ s.title }}
                        </CardTitle>
                        <CardDescription>{{ s.description }}</CardDescription>
                    </div>
                    <Button
                        :class="toneClass(s.tone, 'button')"
                        :disabled="pending !== null"
                        @click="clear(s.type)"
                    >
                        <Loader2 v-if="pending === s.type" class="size-4 animate-spin" />
                        <Trash2 v-else class="size-4" />
                        {{ t('system.cache_flush_everything') }}
                    </Button>
                </CardHeader>
                <CardContent v-if="clearedAt[s.type]" class="pb-4 pt-0">
                    <p class="text-xs text-muted-foreground">
                        {{ t('system.cache_last_cleared', { time: clearedAt[s.type] }) }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Framework caches (sky) -->
        <div v-if="grouped.sky.length > 0" class="space-y-3">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                <RefreshCw class="size-4" />
                {{ t('system.cache_framework_caches') }}
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <Card v-for="s in grouped.sky" :key="s.type">
                    <CardHeader class="space-y-2">
                        <CardTitle class="flex items-center gap-2 text-base">
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase" :class="toneClass(s.tone, 'badge')">{{ s.type }}</span>
                            {{ s.title }}
                        </CardTitle>
                        <CardDescription class="text-xs">{{ s.description }}</CardDescription>
                    </CardHeader>
                    <CardContent class="flex items-center justify-between gap-3">
                        <span v-if="clearedAt[s.type]" class="text-xs text-muted-foreground">
                            {{ t('system.cache_cleared_at', { time: clearedAt[s.type] }) }}
                        </span>
                        <span v-else class="text-xs text-muted-foreground/60">{{ t('system.cache_idle') }}</span>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="pending !== null"
                            @click="clear(s.type)"
                        >
                            <Loader2 v-if="pending === s.type" class="size-4 animate-spin" />
                            <Trash2 v-else class="size-4" />
                            {{ t('system.cache_clear_btn') }}
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Application caches (violet) -->
        <div v-if="grouped.violet.length > 0" class="space-y-3">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                <Database class="size-4" />
                {{ t('system.cache_application_caches') }}
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <Card v-for="s in grouped.violet" :key="s.type">
                    <CardHeader class="space-y-2">
                        <CardTitle class="flex items-center gap-2 text-base">
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase" :class="toneClass(s.tone, 'badge')">{{ s.type }}</span>
                            {{ s.title }}
                        </CardTitle>
                        <CardDescription class="text-xs">{{ s.description }}</CardDescription>
                    </CardHeader>
                    <CardContent class="flex items-center justify-between gap-3">
                        <span v-if="clearedAt[s.type]" class="text-xs text-muted-foreground">
                            {{ t('system.cache_cleared_at', { time: clearedAt[s.type] }) }}
                        </span>
                        <span v-else class="text-xs text-muted-foreground/60">{{ t('system.cache_idle') }}</span>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="pending !== null"
                            @click="clear(s.type)"
                        >
                            <Loader2 v-if="pending === s.type" class="size-4 animate-spin" />
                            <Trash2 v-else class="size-4" />
                            {{ t('system.cache_clear_btn') }}
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
