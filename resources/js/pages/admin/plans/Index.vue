<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Boxes, Check, Crown, Minus, Pencil, Users } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('plans.title'), href: '/admin/plans' },
]);

type Plan = {
    slug: 'basic' | 'premium' | 'gold';
    label: string;
    price: number;
    currency: string;
    period: string;
    positioning: string | null;
    placement: string;
    features: Record<string, boolean>;
    caps: Record<string, number | null>;
    sort_order: number;
    is_active: boolean;
    is_free: boolean;
    company_count: number;
};

defineProps<{ plans: Plan[] }>();

function placementVariant(p: string): 'default' | 'secondary' | 'outline' {
    if (p === 'featured') return 'default';
    if (p === 'boosted') return 'secondary';
    return 'outline';
}

const FEATURE_KEYS = [
    'short_description', 'about', 'founded', 'employees',
    'faqs', 'cover', 'trust', 'google',
] as const;

const CAP_KEYS = ['contacts', 'services', 'areas', 'gallery', 'faqs', 'reply_reviews'] as const;

function capDisplay(v: number | null | undefined): string {
    if (v === null || v === undefined) return t('plans.unlimited');
    return String(v);
}
</script>

<template>
    <Head :title="t('plans.title')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading :title="t('plans.title')" :description="t('plans.description')" />
        </div>

        <!-- Warning banner: edits are live for every existing partner. -->
        <div class="rounded-lg border border-amber-300 bg-amber-50 p-3 text-xs text-amber-900">
            {{ t('plans.warning') }}
        </div>

        <!-- 3 plan cards (edit-only — slugs are locked to the PlanTier enum). -->
        <div class="grid gap-4 md:grid-cols-3">
            <Card v-for="plan in plans" :key="plan.slug" class="flex flex-col">
                <CardContent class="flex flex-1 flex-col gap-3 p-5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                {{ plan.slug }}
                            </p>
                            <p class="mt-0.5 flex items-center gap-1.5 text-xl font-bold">
                                <Crown v-if="plan.slug === 'gold'" class="size-5 text-amber-500" />
                                <Boxes v-else class="size-5 text-muted-foreground" />
                                {{ plan.label }}
                            </p>
                        </div>
                        <Badge :variant="placementVariant(plan.placement)" class="capitalize">
                            {{ plan.placement }}
                        </Badge>
                    </div>

                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-bold">
                            {{ plan.is_free ? t('plans.free') : `${plan.currency}${plan.price}` }}
                        </span>
                        <span v-if="!plan.is_free" class="text-xs text-muted-foreground">
                            / {{ plan.period }}
                        </span>
                    </div>

                    <p class="min-h-[36px] text-xs text-muted-foreground">
                        {{ plan.positioning ?? '—' }}
                    </p>

                    <!-- Live count of companies on this plan. -->
                    <p class="flex items-center gap-1.5 text-[11px] text-muted-foreground">
                        <Users class="size-3.5" />
                        {{ t('plans.company_count', { n: plan.company_count }) }}
                    </p>

                    <!-- Compact feature checklist. -->
                    <div class="mt-1 space-y-1 border-t pt-3">
                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                            {{ t('plans.features') }}
                        </p>
                        <ul class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-[11px]">
                            <li
                                v-for="k in FEATURE_KEYS"
                                :key="k"
                                class="flex items-center gap-1"
                                :class="plan.features[k] ? '' : 'text-muted-foreground line-through'"
                            >
                                <Check v-if="plan.features[k]" class="size-3 text-emerald-600" />
                                <Minus v-else class="size-3 text-muted-foreground" />
                                {{ t(`plans.feat_${k}`) }}
                            </li>
                        </ul>
                    </div>

                    <!-- Compact caps grid. -->
                    <div class="space-y-1 border-t pt-3">
                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                            {{ t('plans.caps') }}
                        </p>
                        <ul class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-[11px]">
                            <li v-for="k in CAP_KEYS" :key="k" class="flex items-center justify-between">
                                <span class="text-muted-foreground">{{ t(`plans.cap_${k}`) }}</span>
                                <span class="font-medium">{{ capDisplay(plan.caps[k]) }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-auto pt-3">
                        <Button as-child variant="outline" class="w-full">
                            <Link :href="`/admin/plans/${plan.slug}/edit`">
                                <Pencil class="size-3.5" />
                                {{ t('plans.edit') }}
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
