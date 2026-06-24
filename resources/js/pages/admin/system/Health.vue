<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    AlertTriangle,
    CheckCircle2,
    RefreshCw,
    XCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';
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
import { useT } from '@/composables/useT';

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.system'), href: '/admin/system/cache' },
    { title: t('sidebar.health'), href: '/admin/system/health' },
]);

type Status = 'ok' | 'warn' | 'fail';

type Check = {
    id: string;
    label: string;
    status: Status;
    detail: string;
    value: string | null;
};

const props = defineProps<{
    checks: Check[];
    summary: { ok: number; warn: number; fail: number };
}>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

function refresh(): void {
    router.reload({ only: ['checks', 'summary'] });
}

const overall = computed<Status>(() => {
    if (props.summary.fail > 0) return 'fail';
    if (props.summary.warn > 0) return 'warn';
    return 'ok';
});

const overallCopy = computed<{ title: string; subtitle: string }>(() => {
    if (overall.value === 'fail') {
        return {
            title: t('system.health_action_required'),
            subtitle: t('system.health_failing_count', { count: props.summary.fail }),
        };
    }
    if (overall.value === 'warn') {
        return {
            title: t('system.health_investigate_warnings'),
            subtitle: t('system.health_warning_count', { count: props.summary.warn }),
        };
    }
    return {
        title: t('system.health_all_clear'),
        subtitle: t('system.health_every_passing'),
    };
});

const statusLabel: Record<Status, string> = {
    ok: t('system.health_status_ok'),
    warn: t('system.health_status_warn'),
    fail: t('system.health_status_fail'),
};

const TONE: Record<
    Status,
    { dot: string; row: string; icon: typeof CheckCircle2; chip: string }
> = {
    ok: {
        dot: 'bg-emerald-500',
        row: 'border-emerald-200 dark:border-emerald-900',
        icon: CheckCircle2,
        chip: 'text-emerald-700 bg-emerald-50 dark:text-emerald-300 dark:bg-emerald-950',
    },
    warn: {
        dot: 'bg-amber-500',
        row: 'border-amber-200 dark:border-amber-900',
        icon: AlertTriangle,
        chip: 'text-amber-700 bg-amber-50 dark:text-amber-300 dark:bg-amber-950',
    },
    fail: {
        dot: 'bg-rose-500',
        row: 'border-rose-200 dark:border-rose-900',
        icon: XCircle,
        chip: 'text-rose-700 bg-rose-50 dark:text-rose-300 dark:bg-rose-950',
    },
};
</script>

<template>
    <Head :title="t('system.health_title')" />

    <div class="space-y-6 p-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <Heading
                :title="t('system.health_title')"
                :description="t('system.health_description')"
            />
            <Button
                type="button"
                variant="outline"
                size="sm"
                @click="refresh"
            >
                <RefreshCw class="size-3.5" />
                {{ t('system.health_recheck') }}
            </Button>
        </div>

        <!-- Overall banner -->
        <Card
            :class="[
                overall === 'ok' && 'border-emerald-200 dark:border-emerald-900',
                overall === 'warn' && 'border-amber-200 dark:border-amber-900',
                overall === 'fail' && 'border-rose-200 dark:border-rose-900',
            ]"
        >
            <CardContent
                class="flex flex-wrap items-center justify-between gap-4 p-4"
            >
                <div class="flex items-center gap-3">
                    <component
                        :is="TONE[overall].icon"
                        class="size-6"
                        :class="
                            overall === 'ok'
                                ? 'text-emerald-600'
                                : overall === 'warn'
                                  ? 'text-amber-600'
                                  : 'text-rose-600'
                        "
                    />
                    <div>
                        <p class="font-semibold">
                            {{ overallCopy.title }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ overallCopy.subtitle }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span
                        class="rounded-full px-2 py-1"
                        :class="TONE.ok.chip"
                    >
                        {{ t('system.health_pill_ok', { count: summary.ok }) }}
                    </span>
                    <span
                        class="rounded-full px-2 py-1"
                        :class="TONE.warn.chip"
                    >
                        {{ t('system.health_pill_warn', { count: summary.warn }) }}
                    </span>
                    <span
                        class="rounded-full px-2 py-1"
                        :class="TONE.fail.chip"
                    >
                        {{ t('system.health_pill_fail', { count: summary.fail }) }}
                    </span>
                </div>
            </CardContent>
        </Card>

        <!-- Check rows -->
        <Card>
            <CardHeader>
                <CardTitle class="text-base">{{ t('system.health_checks_title') }}</CardTitle>
                <CardDescription class="text-xs">
                    {{ t('system.health_checks_description') }}
                </CardDescription>
            </CardHeader>
            <CardContent class="p-0">
                <ul class="divide-y">
                    <li
                        v-for="check in checks"
                        :key="check.id"
                        class="flex flex-wrap items-start gap-4 px-4 py-4"
                    >
                        <div class="flex items-center gap-3">
                            <span
                                class="block size-2 rounded-full"
                                :class="TONE[check.status].dot"
                            />
                            <component
                                :is="TONE[check.status].icon"
                                class="size-4"
                                :class="
                                    check.status === 'ok'
                                        ? 'text-emerald-600'
                                        : check.status === 'warn'
                                          ? 'text-amber-600'
                                          : 'text-rose-600'
                                "
                            />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-baseline gap-2">
                                <p class="text-sm font-medium">
                                    {{ check.label }}
                                </p>
                                <span
                                    v-if="check.value"
                                    class="rounded border bg-muted/60 px-1.5 py-0.5 font-mono text-[11px] text-muted-foreground"
                                >
                                    {{ check.value }}
                                </span>
                            </div>
                            <p
                                class="mt-1 text-xs leading-relaxed text-muted-foreground"
                            >
                                {{ check.detail }}
                            </p>
                        </div>
                        <span
                            class="rounded-full px-2 py-1 text-[10px] font-semibold tracking-wide uppercase"
                            :class="TONE[check.status].chip"
                        >
                            {{ statusLabel[check.status] }}
                        </span>
                    </li>
                </ul>
            </CardContent>
        </Card>
    </div>
</template>
