<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';

type Feature = { label: string; included: boolean };
type Plan = {
    name: string;
    price: string;
    icon: string;
    cta_label: string;
    cta_url: string;
    features: Feature[];
};

type Data = {
    eyebrow: string;
    heading: string;
    plans: Plan[];
};

defineModel<Record<string, unknown>>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addPlan(): void {
    data.value.plans = [
        ...(data.value.plans ?? []),
        {
            name: '',
            price: '',
            icon: 'Package',
            cta_label: 'Anfragen',
            cta_url: '#angebot',
            features: [],
        },
    ];
}

function removePlan(index: number): void {
    data.value.plans = (data.value.plans ?? []).filter((_, i) => i !== index);
}

function addFeature(planIndex: number): void {
    const plan = data.value.plans[planIndex];
    plan.features = [...(plan.features ?? []), { label: '', included: true }];
}

function removeFeature(planIndex: number, featureIndex: number): void {
    const plan = data.value.plans[planIndex];
    plan.features = (plan.features ?? []).filter((_, i) => i !== featureIndex);
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input v-model="data.heading" />
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Plans</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addPlan"
                >
                    <Plus class="size-4" />
                    Add plan
                </Button>
            </div>

            <div
                v-for="(plan, pi) in data.plans ?? []"
                :key="pi"
                class="space-y-3 rounded-md border bg-muted/30 p-4"
            >
                <div class="grid gap-3 md:grid-cols-3">
                    <Input v-model="plan.name" placeholder="Name" />
                    <Input
                        v-model="plan.price"
                        placeholder="Price (e.g. €350)"
                    />
                    <Input v-model="plan.icon" placeholder="Icon (lucide)" />
                    <Input
                        v-model="plan.cta_label"
                        placeholder="Button label"
                    />
                    <Input
                        v-model="plan.cta_url"
                        placeholder="Button link"
                        class="md:col-span-2"
                    />
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label class="text-xs font-semibold">Features</Label>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addFeature(pi)"
                        >
                            <Plus class="size-4" />
                            Add feature
                        </Button>
                    </div>
                    <div
                        v-for="(feature, fi) in plan.features ?? []"
                        :key="fi"
                        class="flex items-center gap-3"
                    >
                        <Input
                            v-model="feature.label"
                            placeholder="Feature"
                            class="flex-1"
                        />
                        <label class="flex items-center gap-2 text-xs">
                            <Switch
                                :model-value="feature.included"
                                @update:model-value="
                                    (v) => (feature.included = v)
                                "
                            />
                            Included
                        </label>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="removeFeature(pi, fi)"
                        >
                            <Trash2 class="size-4 text-destructive" />
                        </Button>
                    </div>
                </div>

                <div class="flex justify-end">
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        @click="removePlan(pi)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                        Remove plan
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
