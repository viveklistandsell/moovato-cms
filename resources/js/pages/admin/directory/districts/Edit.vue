<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';
import { slugify } from '@/lib/slug';

const t = useT();

type District = {
    id: number;
    city_id: number;
    state_id: number | null;
    country_id: number | null;
    name: string;
    code: string | null;
    permalink: string;
    postal_code_prefix: string | null;
    is_popular: boolean;
    status: string;
    sort_order: number;
};

type Country = { id: number; name: string; iso_code: string };
type State = { id: number; country_id: number; name: string; code: string };
type City = { id: number; state_id: number; name: string; permalink: string };

const props = defineProps<{
    district: District | null;
    countries: Country[];
    states: State[];
    cities: City[];
    nextSortOrder: number;
}>();

const isEdit = computed(() => props.district !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('locations.districts_title'), href: '/admin/directory/districts' },
    {
        title: isEdit.value
            ? t('locations.district_edit_title')
            : t('locations.district_create_title'),
        href: '#',
    },
]);

const selectedCountryId = ref<number>(
    props.district?.country_id ?? props.countries[0]?.id ?? 0,
);
const selectedStateId = ref<number>(
    props.district?.state_id ?? props.states[0]?.id ?? 0,
);

const form = useForm({
    city_id: props.district?.city_id ?? props.cities[0]?.id ?? 0,
    name: props.district?.name ?? '',
    code: props.district?.code ?? '',
    permalink: props.district?.permalink ?? '',
    postal_code_prefix: props.district?.postal_code_prefix ?? '',
    is_popular: props.district?.is_popular ?? false,
    status: props.district?.status ?? 'published',
    sort_order: props.district?.sort_order ?? props.nextSortOrder,
});

watch(selectedCountryId, (countryId) => {
    if (!countryId) return;
    router.reload({
        only: ['states', 'cities'],
        data: { country_id: countryId },
        onSuccess: () => {
            const stillValidState = props.states.some((s) => s.id === selectedStateId.value);
            if (!stillValidState) {
                selectedStateId.value = props.states[0]?.id ?? 0;
            }
            const stillValidCity = props.cities.some((c) => c.id === form.city_id);
            if (!stillValidCity) {
                form.city_id = props.cities[0]?.id ?? 0;
            }
        },
    });
});

// When state changes, refetch cities scoped to it.
watch(selectedStateId, (stateId) => {
    if (!stateId) return;
    router.reload({
        only: ['cities'],
        data: { state_id: stateId, country_id: selectedCountryId.value },
        onSuccess: () => {
            const stillValid = props.cities.some((c) => c.id === form.city_id);
            if (!stillValid) {
                form.city_id = props.cities[0]?.id ?? 0;
            }
        },
    });
});

// Auto-derive permalink from name — only until the admin manually edits it.
let permalinkTouched = isEdit.value;
watch(
    () => form.permalink,
    (val, oldVal) => {
        if (oldVal !== undefined && val !== oldVal) {
            permalinkTouched = true;
        }
    },
);
watch(
    () => form.name,
    (val) => {
        if (!permalinkTouched && val) {
            form.permalink = slugify(val);
        }
    },
);

function submit(): void {
    if (isEdit.value && props.district) {
        form.put(`/admin/directory/districts/${props.district.id}`);
    } else {
        form.post('/admin/directory/districts');
    }
}
</script>

<template>
    <Head
        :title="
            isEdit
                ? t('locations.district_edit_title')
                : t('locations.district_create_title')
        "
    />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="
                    isEdit
                        ? t('locations.district_edit_title')
                        : t('locations.district_create_title')
                "
                :description="
                    isEdit
                        ? t('locations.district_edit_description')
                        : t('locations.district_create_description')
                "
            />
            <Button as-child variant="ghost">
                <Link href="/admin/directory/districts">
                    <ArrowLeft class="size-4" />
                    {{ t('common.back') }}
                </Link>
            </Button>
        </div>

        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('locations.district_form_title') }}</CardTitle>
                    <CardDescription>
                        {{ t('locations.district_form_description') }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-5">
                    <!-- Country → State → City cascade -->
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="grid gap-2">
                            <Label>{{ t('locations.field_country') }}</Label>
                            <Select
                                :model-value="String(selectedCountryId)"
                                @update:model-value="(v) => (selectedCountryId = Number(v))"
                            >
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="c in countries"
                                        :key="c.id"
                                        :value="String(c.id)"
                                    >
                                        {{ c.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p class="text-xs text-muted-foreground">
                                {{ t('locations.field_country_hint_district') }}
                            </p>
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('locations.field_state') }}</Label>
                            <Select
                                :model-value="String(selectedStateId)"
                                @update:model-value="(v) => (selectedStateId = Number(v))"
                            >
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="s in states"
                                        :key="s.id"
                                        :value="String(s.id)"
                                    >
                                        {{ s.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-2">
                            <Label>
                                {{ t('locations.field_city') }}
                                <span class="text-destructive">*</span>
                            </Label>
                            <Select
                                :model-value="String(form.city_id)"
                                @update:model-value="(v) => (form.city_id = Number(v))"
                            >
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="c in cities"
                                        :key="c.id"
                                        :value="String(c.id)"
                                    >
                                        {{ c.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.city_id" />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">
                                {{ t('locations.field_name') }}
                                <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                :placeholder="t('locations.field_name_placeholder_district')"
                            />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="code">{{ t('locations.field_code') }}</Label>
                            <Input
                                id="code"
                                v-model="form.code"
                                :placeholder="t('locations.field_code_placeholder_district')"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ t('locations.field_code_hint_district') }}
                            </p>
                            <InputError :message="form.errors.code" />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="permalink">
                                {{ t('locations.field_permalink') }}
                                <span class="text-destructive">*</span>
                            </Label>
                            <Input id="permalink" v-model="form.permalink" />
                            <p class="text-xs text-muted-foreground">
                                {{ t('locations.field_permalink_hint_district') }}
                            </p>
                            <InputError :message="form.errors.permalink" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="postal_code_prefix">
                                {{ t('locations.field_postal_code_prefix') }}
                            </Label>
                            <Input
                                id="postal_code_prefix"
                                v-model="form.postal_code_prefix"
                                :placeholder="t('locations.field_postal_code_prefix_placeholder')"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ t('locations.field_postal_code_prefix_hint') }}
                            </p>
                            <InputError :message="form.errors.postal_code_prefix" />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="grid gap-2">
                            <Label>{{ t('table.col_status') }} *</Label>
                            <Select v-model="form.status">
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="published">
                                        {{ t('status.published') }}
                                    </SelectItem>
                                    <SelectItem value="draft">
                                        {{ t('status.draft') }}
                                    </SelectItem>
                                    <SelectItem value="inactive">
                                        {{ t('status.inactive') }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-2">
                            <Label for="sort_order">{{ t('table.col_order') }}</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                                max="65535"
                            />
                            <InputError :message="form.errors.sort_order" />
                        </div>
                        <div class="flex items-end gap-3">
                            <div class="flex items-center gap-2">
                                <Switch
                                    id="is_popular"
                                    :model-value="form.is_popular"
                                    @update:model-value="(v) => (form.is_popular = Boolean(v))"
                                />
                                <Label for="is_popular">
                                    {{ t('locations.field_is_popular_district') }}
                                </Label>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="mt-6 flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    {{ isEdit ? t('common.save_changes') : t('common.create') }}
                </Button>
                <Button as-child type="button" variant="outline">
                    <Link href="/admin/directory/districts">
                        {{ t('common.cancel') }}
                    </Link>
                </Button>
            </div>
        </form>
    </div>
</template>
