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

type City = {
    id: number;
    state_id: number;
    country_id: number | null;
    name: string;
    permalink: string;
    postal_code: string | null;
    is_popular: boolean;
    status: string;
    sort_order: number;
};

type Country = { id: number; name: string; iso_code: string };
type State = { id: number; country_id: number; name: string; code: string };

const props = defineProps<{
    city: City | null;
    countries: Country[];
    states: State[];
    nextSortOrder: number;
}>();

const isEdit = computed(() => props.city !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.cities'), href: '/admin/directory/cities' },
    {
        title: isEdit.value
            ? t('locations.city_edit_title')
            : t('locations.city_create_title'),
        href: '#',
    },
]);

// Country selector is local-only — it filters the states dropdown but is
// not stored on the City row (state already implies country via FK).
const selectedCountryId = ref<number>(
    props.city?.country_id ?? props.countries[0]?.id ?? 0,
);

const form = useForm({
    state_id: props.city?.state_id ?? props.states[0]?.id ?? 0,
    name: props.city?.name ?? '',
    permalink: props.city?.permalink ?? '',
    postal_code: props.city?.postal_code ?? '',
    is_popular: props.city?.is_popular ?? false,
    status: props.city?.status ?? 'published',
    sort_order: props.city?.sort_order ?? props.nextSortOrder,
});

// Reload the states dropdown when the country changes — Inertia partial
// reload swaps the `states` prop scoped to the new country.
watch(selectedCountryId, (countryId) => {
    if (!countryId) return;
    router.reload({
        only: ['states'],
        data: { country_id: countryId },
        onSuccess: () => {
            // If the previously selected state is no longer in the list,
            // reset to the first available one.
            const stillValid = props.states.some((s) => s.id === form.state_id);
            if (!stillValid) {
                form.state_id = props.states[0]?.id ?? 0;
            }
        },
    });
});

// Auto-slugify name → permalink while creating, stop once user edits permalink.
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
    if (isEdit.value && props.city) {
        form.put(`/admin/directory/cities/${props.city.id}`);
    } else {
        form.post('/admin/directory/cities');
    }
}
</script>

<template>
    <Head
        :title="
            isEdit
                ? t('locations.city_edit_title')
                : t('locations.city_create_title')
        "
    />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="
                    isEdit
                        ? t('locations.city_edit_title')
                        : t('locations.city_create_title')
                "
                :description="
                    isEdit
                        ? t('locations.city_edit_description')
                        : t('locations.city_create_description')
                "
            />
            <Button as-child variant="ghost">
                <Link href="/admin/directory/cities">
                    <ArrowLeft class="size-4" />
                    {{ t('common.back') }}
                </Link>
            </Button>
        </div>

        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('locations.city_form_title') }}</CardTitle>
                    <CardDescription>
                        {{ t('locations.city_form_description') }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="country_id">{{ t('locations.field_country') }} *</Label>
                        <Select
                            :model-value="String(selectedCountryId)"
                            @update:model-value="(v) => (selectedCountryId = Number(v))"
                        >
                            <SelectTrigger id="country_id">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="c in countries" :key="c.id" :value="String(c.id)">
                                    {{ c.name }} ({{ c.iso_code }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p class="text-xs text-muted-foreground">
                            {{ t('locations.field_country_hint_city') }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="state_id">{{ t('locations.field_state') }} *</Label>
                        <Select
                            :model-value="String(form.state_id)"
                            @update:model-value="(v) => (form.state_id = Number(v))"
                        >
                            <SelectTrigger id="state_id">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="s in states" :key="s.id" :value="String(s.id)">
                                    {{ s.name }} ({{ s.code }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.state_id" />
                    </div>

                    <div class="space-y-2 sm:col-span-2">
                        <Label for="name">{{ t('locations.field_name') }} *</Label>
                        <Input id="name" v-model="form.name" type="text" autocomplete="off" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="permalink">{{ t('locations.field_permalink') }} *</Label>
                        <Input
                            id="permalink"
                            v-model="form.permalink"
                            type="text"
                            placeholder="berlin"
                            class="font-mono"
                            autocomplete="off"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ t('locations.field_permalink_hint_city') }}
                        </p>
                        <InputError :message="form.errors.permalink" />
                    </div>

                    <div class="space-y-2">
                        <Label for="postal_code">{{ t('locations.field_postal_code') }}</Label>
                        <Input
                            id="postal_code"
                            v-model="form.postal_code"
                            type="text"
                            maxlength="16"
                            placeholder="10115"
                            class="font-mono"
                            autocomplete="off"
                        />
                        <InputError :message="form.errors.postal_code" />
                    </div>

                    <div class="space-y-2 sm:col-span-2">
                        <div class="flex items-center justify-between rounded-md border p-3">
                            <div>
                                <Label for="is_popular" class="text-sm font-medium">
                                    {{ t('locations.field_is_popular') }}
                                </Label>
                                <p class="text-xs text-muted-foreground">
                                    {{ t('locations.field_is_popular_hint') }}
                                </p>
                            </div>
                            <Switch id="is_popular" v-model="form.is_popular" />
                        </div>
                        <InputError :message="form.errors.is_popular" />
                    </div>

                    <div class="space-y-2">
                        <Label for="status">{{ t('table.col_status') }} *</Label>
                        <Select v-model="form.status">
                            <SelectTrigger id="status">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="published">{{ t('status.published') }}</SelectItem>
                                <SelectItem value="draft">{{ t('status.draft') }}</SelectItem>
                                <SelectItem value="inactive">{{ t('status.inactive') }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.status" />
                    </div>

                    <div class="space-y-2">
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
                </CardContent>
            </Card>

            <div class="mt-4 flex items-center justify-end gap-2">
                <Button as-child variant="ghost">
                    <Link href="/admin/directory/cities">
                        {{ t('common.cancel') }}
                    </Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    {{ isEdit ? t('common.save_changes') : t('common.create') }}
                </Button>
            </div>
        </form>
    </div>
</template>
