<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { computed } from 'vue';
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

const t = useT();

type Country = {
    id: number;
    name: string;
    iso_code: string;
    phone_code: string | null;
    status: string;
    sort_order: number;
    states_count: number;
};

const props = defineProps<{
    country: Country | null;
    nextSortOrder: number;
}>();

const isEdit = computed(() => props.country !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.countries'), href: '/admin/directory/countries' },
    {
        title: isEdit.value
            ? t('locations.country_edit_title')
            : t('locations.country_create_title'),
        href: '#',
    },
]);

const form = useForm({
    name: props.country?.name ?? '',
    iso_code: props.country?.iso_code ?? '',
    phone_code: props.country?.phone_code ?? '',
    status: props.country?.status ?? 'published',
    sort_order: props.country?.sort_order ?? props.nextSortOrder,
});

function submit(): void {
    if (isEdit.value && props.country) {
        form.put(`/admin/directory/countries/${props.country.id}`);
    } else {
        form.post('/admin/directory/countries');
    }
}
</script>

<template>
    <Head
        :title="
            isEdit
                ? t('locations.country_edit_title')
                : t('locations.country_create_title')
        "
    />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="
                    isEdit
                        ? t('locations.country_edit_title')
                        : t('locations.country_create_title')
                "
                :description="
                    isEdit
                        ? t('locations.country_edit_description')
                        : t('locations.country_create_description')
                "
            />
            <Button as-child variant="ghost">
                <Link href="/admin/directory/countries">
                    <ArrowLeft class="size-4" />
                    {{ t('common.back') }}
                </Link>
            </Button>
        </div>

        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('locations.country_form_title') }}</CardTitle>
                    <CardDescription>
                        {{ t('locations.country_form_description') }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2 sm:col-span-2">
                        <Label for="name">{{ t('locations.field_name') }} *</Label>
                        <Input id="name" v-model="form.name" type="text" autocomplete="off" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="iso_code">{{ t('locations.field_iso_code') }} *</Label>
                        <Input
                            id="iso_code"
                            v-model="form.iso_code"
                            type="text"
                            maxlength="2"
                            placeholder="DE"
                            class="font-mono uppercase"
                            autocomplete="off"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ t('locations.field_iso_code_hint') }}
                        </p>
                        <InputError :message="form.errors.iso_code" />
                    </div>

                    <div class="space-y-2">
                        <Label for="phone_code">{{ t('locations.field_phone_code') }}</Label>
                        <Input
                            id="phone_code"
                            v-model="form.phone_code"
                            type="text"
                            placeholder="+49"
                            class="font-mono"
                            autocomplete="off"
                        />
                        <InputError :message="form.errors.phone_code" />
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
                    <Link href="/admin/directory/countries">
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
