<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { computed, watch } from 'vue';
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
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';
import { slugify } from '@/lib/slug';

const t = useT();

type State = {
    id: number;
    country_id: number;
    name: string;
    code: string;
    permalink: string;
    status: string;
    sort_order: number;
};

type Country = { id: number; name: string; iso_code: string };

const props = defineProps<{
    state: State | null;
    countries: Country[];
    nextSortOrder: number;
}>();

const isEdit = computed(() => props.state !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.states'), href: '/admin/directory/states' },
    {
        title: isEdit.value
            ? t('locations.state_edit_title')
            : t('locations.state_create_title'),
        href: '#',
    },
]);

const defaultCountryId = props.state?.country_id ?? props.countries[0]?.id ?? 0;

const form = useForm({
    country_id: defaultCountryId,
    name: props.state?.name ?? '',
    code: props.state?.code ?? '',
    permalink: props.state?.permalink ?? '',
    status: props.state?.status ?? 'published',
    sort_order: props.state?.sort_order ?? props.nextSortOrder,
});

/**
 * Auto-sync the permalink from the name UNTIL the admin manually types
 * something different in the permalink field.
 */
let permalinkTouched = isEdit.value && slugify(form.name) !== form.permalink;

function onPermalinkInput(): void {
    permalinkTouched = true;
}

watch(
    () => form.name,
    (val) => {
        if (!permalinkTouched && val) {
            form.permalink = slugify(val);
        }
    },
);

function submit(): void {
    if (isEdit.value && props.state) {
        form.put(`/admin/directory/states/${props.state.id}`);
    } else {
        form.post('/admin/directory/states');
    }
}
</script>

<template>
    <Head
        :title="
            isEdit
                ? t('locations.state_edit_title')
                : t('locations.state_create_title')
        "
    />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="
                    isEdit
                        ? t('locations.state_edit_title')
                        : t('locations.state_create_title')
                "
                :description="
                    isEdit
                        ? t('locations.state_edit_description')
                        : t('locations.state_create_description')
                "
            />
            <Button as-child variant="ghost">
                <Link href="/admin/directory/states">
                    <ArrowLeft class="size-4" />
                    {{ t('common.back') }}
                </Link>
            </Button>
        </div>

        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('locations.state_form_title') }}</CardTitle>
                    <CardDescription>
                        {{ t('locations.state_form_description') }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2 sm:col-span-2">
                        <Label for="country_id">{{ t('locations.field_country') }} *</Label>
                        <Select
                            :model-value="String(form.country_id)"
                            @update:model-value="(v) => (form.country_id = Number(v))"
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
                        <InputError :message="form.errors.country_id" />
                    </div>

                    <div class="space-y-2">
                        <Label for="name">{{ t('locations.field_name') }} *</Label>
                        <Input id="name" v-model="form.name" type="text" autocomplete="off" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="code">{{ t('locations.field_state_code') }} *</Label>
                        <Input
                            id="code"
                            v-model="form.code"
                            type="text"
                            maxlength="8"
                            placeholder="BE"
                            class="font-mono uppercase"
                            autocomplete="off"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ t('locations.field_state_code_hint') }}
                        </p>
                        <InputError :message="form.errors.code" />
                    </div>

                    <div class="space-y-2 sm:col-span-2">
                        <Label for="permalink">{{ t('locations.field_permalink') }} *</Label>
                        <Input
                            id="permalink"
                            v-model="form.permalink"
                            type="text"
                            placeholder="berlin"
                            class="font-mono"
                            autocomplete="off"
                            @input="onPermalinkInput"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ t('locations.field_permalink_hint_state') }}
                        </p>
                        <InputError :message="form.errors.permalink" />
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
                    <Link href="/admin/directory/states">
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
