<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, X } from 'lucide-vue-next';
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
import { Switch } from '@/components/ui/switch';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';

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
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Languages', href: '/admin/languages' },
    {
        title: isEdit.value ? 'Edit language' : 'Add a language',
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
    <Head :title="isEdit ? 'Edit language' : 'Add language'" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center gap-3">
            <Button as-child variant="ghost" size="sm">
                <Link href="/admin/languages">
                    <ArrowLeft class="size-4" />
                </Link>
            </Button>
            <Heading
                :title="isEdit ? 'Edit language' : 'Add a language'"
                :description="
                    isEdit
                        ? 'Update locale details and visibility.'
                        : 'Pick a code (e.g. de, en, fr-FR) and the human-readable names.'
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
                        <CardTitle>Identity</CardTitle>
                        <CardDescription>
                            Code is the URL prefix (e.g. <code>en</code> →
                            <code>/en/blog</code>) and the language tab key.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-5">
                        <div class="grid gap-2">
                            <Label for="code">
                                Code
                                <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="code"
                                v-model="form.code"
                                placeholder="de, en, fr, de_AT…"
                                autocomplete="off"
                            />
                            <p class="text-xs text-muted-foreground">
                                Lowercase ASCII; 2–10 chars. Optional region
                                suffix like <code>de_AT</code> or
                                <code>en-GB</code>.
                            </p>
                            <InputError :message="form.errors.code" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">
                                    Name
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="English"
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="native_name">
                                    Native name
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="native_name"
                                    v-model="form.native_name"
                                    placeholder="Deutsch"
                                />
                                <InputError
                                    :message="form.errors.native_name"
                                />
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="flag">Flag</Label>
                                <Input
                                    id="flag"
                                    v-model="form.flag"
                                    placeholder="🇩🇪 (emoji)"
                                />
                                <InputError :message="form.errors.flag" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="lang_locale">PHP locale</Label>
                                <Input
                                    id="lang_locale"
                                    v-model="form.lang_locale"
                                    placeholder="de_DE, en_US…"
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
                        <CardTitle>Save</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                <Save class="size-4" />
                                Save
                            </Button>
                            <Button
                                as-child
                                type="button"
                                variant="outline"
                                class="flex-1"
                            >
                                <Link href="/admin/languages">
                                    <X class="size-4" />
                                    Cancel
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Settings</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center justify-between">
                            <Label for="status" class="cursor-pointer">
                                Active
                            </Label>
                            <Switch
                                id="status"
                                :model-value="form.status"
                                @update:model-value="(v) => (form.status = v)"
                            />
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Inactive languages are hidden from the public site
                            and from translation tabs.
                        </p>

                        <div class="flex items-center justify-between">
                            <Label for="lang_is_default" class="cursor-pointer">
                                Default language
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
                            The default locale renders at the URL root (no
                            <code>/code/</code> prefix). Enabling this unsets it
                            on every other language.
                        </p>

                        <div class="grid gap-2">
                            <Label for="sort_order">Sort order</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                            />
                            <p class="text-xs text-muted-foreground">
                                Controls the order language tabs appear in Edit
                                forms.
                            </p>
                            <InputError :message="form.errors.sort_order" />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </form>
    </div>
</template>
