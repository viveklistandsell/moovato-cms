<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Lock, Save, X } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';

type PermissionItem = {
    id: number;
    name: string;
    display_name: string;
    description: string | null;
};

type PermissionGroup = {
    group: string;
    items: PermissionItem[];
};

type RoleProp = {
    id: number;
    name: string;
    display_name: string;
    description: string | null;
    color: string;
    is_system: boolean;
    permission_ids: number[];
};

type ColorOption = { value: string; label: string };

const props = defineProps<{
    role: RoleProp | null;
    permissionGroups: PermissionGroup[];
    colorOptions: ColorOption[];
}>();

const isEdit = computed(() => props.role !== null);

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Users', href: '/admin/users' },
            { title: 'Roles', href: '/admin/roles' },
            { title: 'Edit role', href: '#' },
        ],
    },
});

const form = useForm({
    name: props.role?.name ?? '',
    display_name: props.role?.display_name ?? '',
    description: props.role?.description ?? '',
    color: props.role?.color ?? 'sky',
    permissions: (props.role?.permission_ids ?? []) as number[],
});

function togglePermission(id: number, checked: boolean | 'indeterminate'): void {
    const has = form.permissions.includes(id);
    if (checked === true && !has) {
        form.permissions = [...form.permissions, id];
    } else if (checked === false && has) {
        form.permissions = form.permissions.filter((p) => p !== id);
    }
}

function toggleGroup(group: PermissionGroup, checked: boolean): void {
    const groupIds = group.items.map((i) => i.id);
    if (checked) {
        const merged = new Set([...form.permissions, ...groupIds]);
        form.permissions = [...merged];
    } else {
        form.permissions = form.permissions.filter((id) => !groupIds.includes(id));
    }
}

function groupState(group: PermissionGroup): boolean | 'indeterminate' {
    const total = group.items.length;
    const selected = group.items.filter((i) => form.permissions.includes(i.id)).length;
    if (selected === 0) return false;
    if (selected === total) return true;
    return 'indeterminate';
}

function submit(): void {
    if (isEdit.value && props.role !== null) {
        form
            .transform((data) => ({ ...data, _method: 'put' }))
            .post(`/admin/roles/${props.role.id}`, { preserveScroll: true });
    } else {
        form.post('/admin/roles', { preserveScroll: true });
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edit role' : 'New role'" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <Button as-child variant="ghost" size="sm">
                    <Link href="/admin/roles">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    :title="isEdit ? `Edit ${role?.display_name}` : 'Create a new role'"
                    :description="
                        role?.is_system
                            ? 'This is a system role — name is locked, but you can adjust display info + permissions.'
                            : 'Group permissions into a role you can assign to users.'
                    "
                />
            </div>
        </div>

        <form class="grid grid-cols-1 gap-6 lg:grid-cols-3" @submit.prevent="submit">
            <!-- LEFT: identity -->
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Role identity</CardTitle>
                        <CardDescription>
                            Shown in the user table and role picker.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="role-name">
                                Name
                                <Lock v-if="role?.is_system" class="ml-1 inline size-3 text-muted-foreground" />
                            </Label>
                            <Input
                                id="role-name"
                                v-model="form.name"
                                :disabled="role?.is_system === true"
                                placeholder="moderator"
                            />
                            <p class="text-xs text-muted-foreground">
                                Stable identifier used in code (e.g. `moderator`).
                            </p>
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="role-display-name">Display name</Label>
                            <Input
                                id="role-display-name"
                                v-model="form.display_name"
                                placeholder="Content Moderator"
                            />
                            <InputError :message="form.errors.display_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="role-description">Description</Label>
                            <Textarea
                                id="role-description"
                                v-model="form.description"
                                :rows="3"
                                placeholder="Short summary of what this role is for."
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="role-color">Badge color</Label>
                            <Select
                                :model-value="form.color"
                                @update:model-value="(v) => (form.color = v as string)"
                            >
                                <SelectTrigger id="role-color">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opt in colorOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.color" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Save</CardTitle>
                    </CardHeader>
                    <CardContent class="flex flex-wrap items-center gap-2">
                        <Button type="submit" :disabled="form.processing">
                            <Save class="size-4" />
                            {{ isEdit ? 'Save changes' : 'Create role' }}
                        </Button>
                        <Button as-child type="button" variant="outline">
                            <Link href="/admin/roles">
                                <X class="size-4" />
                                Cancel
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <!-- RIGHT: permissions grouped by module -->
            <div class="space-y-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Permissions</CardTitle>
                        <CardDescription>
                            Tick the permissions this role should grant. Use the
                            group header to toggle all permissions in that module.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div
                            v-for="group in permissionGroups"
                            :key="group.group"
                            class="rounded-md border bg-muted/20 p-4"
                        >
                            <div class="mb-3 flex items-center gap-2">
                                <Checkbox
                                    :model-value="groupState(group)"
                                    @update:model-value="
                                        (v) => toggleGroup(group, v === true)
                                    "
                                />
                                <span class="text-sm font-semibold">{{ group.group }}</span>
                                <Badge variant="secondary" class="text-[10px]">
                                    {{ group.items.length }}
                                </Badge>
                            </div>
                            <div class="grid gap-2 sm:grid-cols-2">
                                <label
                                    v-for="perm in group.items"
                                    :key="perm.id"
                                    class="flex items-start gap-2 rounded-md border bg-background px-3 py-2 transition hover:border-primary/50"
                                >
                                    <Checkbox
                                        :model-value="form.permissions.includes(perm.id)"
                                        @update:model-value="
                                            (v) => togglePermission(perm.id, v)
                                        "
                                    />
                                    <div class="flex-1">
                                        <div class="text-sm font-medium">
                                            {{ perm.display_name }}
                                        </div>
                                        <div class="text-[11px] text-muted-foreground">
                                            {{ perm.name }}
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <InputError :message="form.errors.permissions" />
                    </CardContent>
                </Card>
            </div>
        </form>
    </div>
</template>
