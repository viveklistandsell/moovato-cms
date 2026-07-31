<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, X } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import FlagImage from '@/components/common/FlagImage.vue';
import FlagPicker from '@/components/common/FlagPicker.vue';
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
import { Switch } from '@/components/ui/switch';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();

type Language = {
    id: number;
    code: string;
    name: string;
    native_name: string;
    flag: string | null;
    lang_locale: string | null;
    lang_is_default: boolean;
    status: boolean;
    sort_order: number;
};

const props = defineProps<{
    language: Language | null;
    nextSortOrder: number;
}>();

const isEdit = computed(() => props.language !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.languages'), href: '/admin/languages' },
    {
        title: isEdit.value ? t('languages.edit_title') : t('languages.create_title'),
        href: '#',
    },
]);

const form = useForm({
    code: props.language?.code ?? '',
    name: props.language?.name ?? '',
    native_name: props.language?.native_name ?? '',
    flag: props.language?.flag ?? '',
    lang_locale: props.language?.lang_locale ?? '',
    lang_is_default: props.language?.lang_is_default ?? false,
    status: props.language?.status ?? true,
    sort_order: props.language?.sort_order ?? props.nextSortOrder,
});

function submit(): void {
    if (isEdit.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/admin/languages/${props.language!.id}`,
        );
    } else {
        form.post('/admin/languages');
    }
}
</script>

<template>
    <Head :title="isEdit ? t('languages.edit_title') : t('languages.add_button')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center gap-3">
            <Button as-child variant="ghost" size="sm">
                <Link href="/admin/languages">
                    <ArrowLeft class="size-4" />
                </Link>
            </Button>
            <Heading
                :title="isEdit ? t('languages.edit_title') : t('languages.create_title')"
                :description="
                    isEdit
                        ? t('languages.edit_description')
                        : t('languages.create_description')
                "
            />
        </div>

        <form
            class="grid grid-cols-1 gap-6 lg:grid-cols-3"
            @submit.prevent="submit"
        >
            <!-- LEFT: identity -->
            <div class="space-y-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('languages.identity_title') }}</CardTitle>
                        <CardDescription>
                            {{ t('languages.identity_description') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-5">
                        <div class="grid gap-2">
                            <Label for="code">
                                {{ t('languages.code_label') }}
                                <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="code"
                                v-model="form.code"
                                :placeholder="t('languages.code_placeholder')"
                                autocomplete="off"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ t('languages.code_hint') }}
                            </p>
                            <InputError :message="form.errors.code" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">
                                    {{ t('languages.name_label') }}
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    :placeholder="t('languages.name_placeholder')"
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="native_name">
                                    {{ t('languages.native_name_label') }}
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="native_name"
                                    v-model="form.native_name"
                                    :placeholder="t('languages.native_name_placeholder')"
                                />
                                <InputError
                                    :message="form.errors.native_name"
                                />
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="flag">{{ t('languages.flag_label') }}</Label>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="flex h-9 w-11 shrink-0 items-center justify-center rounded-md border bg-muted/40"
                                    >
                                        <FlagImage :code="form.flag" size="md" />
                                    </span>
                                    <Input
                                        id="flag"
                                        v-model="form.flag"
                                        :placeholder="t('languages.flag_placeholder')"
                                        class="flex-1"
                                    />
                                    <FlagPicker
                                        @select="(code) => (form.flag = code)"
                                    />
                                </div>
                                <InputError :message="form.errors.flag" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="lang_locale">{{ t('languages.php_locale_label') }}</Label>
                                <Input
                                    id="lang_locale"
                                    v-model="form.lang_locale"
                                    :placeholder="t('languages.php_locale_placeholder')"
                                />
                                <InputError
                                    :message="form.errors.lang_locale"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- RIGHT: settings -->
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('languages.save_card_title') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                <Save class="size-4" />
                                {{ t('languages.save_button') }}
                            </Button>
                            <Button
                                as-child
                                type="button"
                                variant="outline"
                                class="flex-1"
                            >
                                <Link href="/admin/languages">
                                    <X class="size-4" />
                                    {{ t('languages.cancel_button') }}
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('languages.settings_card_title') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center justify-between">
                            <Label for="status" class="cursor-pointer">
                                {{ t('languages.active_toggle') }}
                            </Label>
                            <Switch
                                id="status"
                                :model-value="form.status"
                                @update:model-value="(v) => (form.status = v)"
                            />
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('languages.active_hint') }}
                        </p>

                        <div class="flex items-center justify-between">
                            <Label for="lang_is_default" class="cursor-pointer">
                                {{ t('languages.default_toggle') }}
                            </Label>
                            <Switch
                                id="lang_is_default"
                                :model-value="form.lang_is_default"
                                @update:model-value="
                                    (v) => (form.lang_is_default = v)
                                "
                            />
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('languages.default_hint') }}
                        </p>

                        <div class="grid gap-2">
                            <Label for="sort_order">{{ t('languages.sort_order_label') }}</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ t('languages.sort_order_hint') }}
                            </p>
                            <InputError :message="form.errors.sort_order" />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </form>
    </div>
</template>
