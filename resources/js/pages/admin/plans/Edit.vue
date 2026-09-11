<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Boxes, Check, Loader2, Users } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();

type Plan = {
    slug: 'basic' | 'premium' | 'gold';
    label: string;
    price: number;
    currency: string;
    period: string;
    positioning: string | null;
    placement: string;
    lead_url: string | null;
    lead_label: string | null;
    features: Record<string, boolean>;
    caps: Record<string, number | null>;
    is_active: boolean;
    is_free: boolean;
};

const props = defineProps<{
    plan: Plan;
    feature_keys: string[];
    cap_keys: string[];
    placements: string[];
    company_count: number;
}>();

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('plans.title'), href: '/admin/plans' },
    { title: props.plan.label, href: `/admin/plans/${props.plan.slug}/edit` },
]);

function capToInput(v: number | null): number | string {
    return v === null ? '' : v;
}

const form = useForm<{
    label: string;
    price: number;
    currency: string;
    period: string;
    positioning: string;
    placement: string;
    lead_url: string;
    lead_label: string;
    is_active: boolean;
    features: Record<string, boolean>;
    caps: Record<string, number | string>;
}>({
    label: props.plan.label,
    price: props.plan.price,
    currency: props.plan.currency,
    period: props.plan.period,
    positioning: props.plan.positioning ?? '',
    placement: props.plan.placement,
    lead_url: props.plan.lead_url ?? '',
    lead_label: props.plan.lead_label ?? '',
    is_active: props.plan.is_active,
    features: props.feature_keys.reduce<Record<string, boolean>>((acc, k) => {
        acc[k] = Boolean(props.plan.features[k]);
        return acc;
    }, {}),
    caps: props.cap_keys.reduce<Record<string, number | string>>((acc, k) => {
        acc[k] = capToInput(props.plan.caps[k] ?? null);
        return acc;
    }, {}),
});

function submit(): void {
    form.patch(`/admin/plans/${props.plan.slug}`, {
        preserveScroll: true,
    });
}

function toggleUnlimited(k: string, checked: boolean | 'indeterminate'): void {
    if (checked === 'indeterminate') return;

    form.caps[k] = checked ? '' : 0;
}
function isUnlimited(k: string): boolean {
    return form.caps[k] === '' || form.caps[k] === null;
}
</script>

<template>
    <Head :title="`${t('plans.edit_title')} · ${plan.label}`" />

    <div class="mx-auto flex max-w-4xl flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <Link
                    href="/admin/plans"
                    class="mb-2 inline-flex items-center gap-1 text-xs text-muted-foreground hover:text-foreground"
                >
                    <ArrowLeft class="size-3.5" />
                    {{ t('plans.back_to_list') }}
                </Link>
                <Heading
                    :title="`${t('plans.edit_title')} — ${plan.label}`"
                    :description="t('plans.edit_description')"
                />
            </div>
        </div>

        <!-- Live-impact warning banner. -->
        <div class="flex items-start gap-2 rounded-lg border border-amber-300 bg-amber-50 p-3 text-xs text-amber-900">
            <Users class="mt-0.5 size-4 shrink-0" />
            <span>{{ t('plans.impact_warning', { n: company_count, tier: plan.label }) }}</span>
        </div>

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <!-- Basic details -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="flex items-center gap-2 text-sm font-medium">
                        <Boxes class="size-4" />
                        {{ t('plans.section_basic') }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium">{{ t('plans.label_name') }}</label>
                        <Input v-model="form.label" :placeholder="plan.slug" />
                        <p v-if="form.errors.label" class="mt-1 text-xs text-destructive">{{ form.errors.label }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium">{{ t('plans.label_placement') }}</label>
                        <Select v-model="form.placement">
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="p in placements" :key="p" :value="p" class="capitalize">
                                    {{ p }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.placement" class="mt-1 text-xs text-destructive">{{ form.errors.placement }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium">{{ t('plans.label_price') }}</label>
                        <Input v-model.number="form.price" type="number" min="0" />
                        <p v-if="form.errors.price" class="mt-1 text-xs text-destructive">{{ form.errors.price }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium">{{ t('plans.label_currency') }}</label>
                        <Input v-model="form.currency" maxlength="8" />
                        <p v-if="form.errors.currency" class="mt-1 text-xs text-destructive">{{ form.errors.currency }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium">{{ t('plans.label_period') }}</label>
                        <Input v-model="form.period" maxlength="32" />
                        <p v-if="form.errors.period" class="mt-1 text-xs text-destructive">{{ form.errors.period }}</p>
                    </div>
                    <div class="flex items-end">
                        <label class="flex cursor-pointer items-center gap-2 text-xs">
                            <Checkbox v-model="form.is_active" />
                            {{ t('plans.label_active') }}
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-medium">{{ t('plans.label_positioning') }}</label>
                        <Input v-model="form.positioning" :placeholder="t('plans.positioning_placeholder')" />
                        <p v-if="form.errors.positioning" class="mt-1 text-xs text-destructive">{{ form.errors.positioning }}</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Features (on/off) -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-sm font-medium">{{ t('plans.section_features') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-2 md:grid-cols-2">
                        <label
                            v-for="k in feature_keys"
                            :key="k"
                            class="flex cursor-pointer items-center gap-2 rounded-md border border-input px-3 py-2 text-sm transition-colors hover:bg-muted/40"
                            :class="form.features[k] ? 'border-emerald-300 bg-emerald-50' : ''"
                        >
                            <Checkbox v-model="form.features[k]" />
                            {{ t(`plans.feat_${k}`) }}
                        </label>
                    </div>
                    <div
                        v-if="form.features.lead"
                        class="mt-4 rounded-md border border-dashed border-[var(--orange)]/40 bg-[var(--orange-soft)]/40 p-3"
                    >
                        <p class="mb-2 text-xs font-semibold text-[var(--midnight)]">
                            {{ t('plans.section_lead') }}
                        </p>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-[11px] font-medium">{{ t('plans.label_lead_url') }}</label>
                                <Input v-model="form.lead_url" :placeholder="t('plans.lead_url_placeholder')" />
                                <p v-if="form.errors.lead_url" class="mt-1 text-xs text-destructive">{{ form.errors.lead_url }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-medium">{{ t('plans.label_lead_label') }}</label>
                                <Input v-model="form.lead_label" :placeholder="t('plans.lead_label_placeholder')" />
                                <p v-if="form.errors.lead_label" class="mt-1 text-xs text-destructive">{{ form.errors.lead_label }}</p>
                            </div>
                        </div>
                        <p class="mt-2 text-[11px] text-muted-foreground">{{ t('plans.lead_url_hint') }}</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Caps (numeric limits) -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-sm font-medium">{{ t('plans.section_caps') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="mb-3 text-xs text-muted-foreground">{{ t('plans.caps_hint') }}</p>
                    <div class="grid gap-3 md:grid-cols-2">
                        <div v-for="k in cap_keys" :key="k" class="rounded-md border border-input p-3">
                            <label class="mb-2 block text-xs font-medium">{{ t(`plans.cap_${k}`) }}</label>
                            <div class="flex items-center gap-2">
                                <Input
                                    v-model.number="form.caps[k]"
                                    type="number"
                                    min="0"
                                    :disabled="isUnlimited(k)"
                                    :placeholder="isUnlimited(k) ? t('plans.unlimited') : '0'"
                                    class="flex-1"
                                />
                                <label class="flex cursor-pointer items-center gap-1.5 text-[11px] text-muted-foreground">
                                    <Checkbox
                                        :model-value="isUnlimited(k)"
                                        @update:model-value="(v: boolean | 'indeterminate') => toggleUnlimited(k, v === true)"
                                    />
                                    {{ t('plans.unlimited') }}
                                </label>
                            </div>
                            <p v-if="form.errors[`caps.${k}` as keyof typeof form.errors]" class="mt-1 text-xs text-destructive">
                                {{ form.errors[`caps.${k}` as keyof typeof form.errors] }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="flex items-center justify-end gap-2">
                <Button as-child variant="ghost">
                    <Link href="/admin/plans">{{ t('plans.cancel') }}</Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                    <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                    <Check v-else class="size-4" />
                    {{ t('plans.save') }}
                </Button>
            </div>
        </form>
    </div>
</template>
