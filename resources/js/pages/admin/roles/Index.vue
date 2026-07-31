<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Lock, Pencil, Plus, ShieldCheck, Trash2 } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
} from '@/components/ui/card';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { usePermissionLabels } from '@/composables/usePermissionLabels';
import { useT } from '@/composables/useT';

const t = useT();
const { roleLabel } = usePermissionLabels();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.user_management'), href: '/admin/users' },
    { title: t('sidebar.roles'), href: '/admin/roles' },
]);

type Role = {
    id: number;
    name: string;
    display_name: string;
    description: string | null;
    color: string;
    is_system: boolean;
    user_count: number;
    permission_count: number;
};

defineProps<{
    roles: Role[];
}>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

function badgeClasses(color: string): string {
    const map: Record<string, string> = {
        rose: 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
        amber: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        emerald: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        sky: 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
        violet: 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300',
        neutral: 'bg-neutral-200 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
    };
    return map[color] ?? map.neutral;
}

function destroy(role: Role): void {
    if (role.is_system) return;
    const name = roleLabel(role.name, role.display_name);
    if (role.user_count > 0) {
        alert(t('roles.cannot_delete_in_use', { name, count: role.user_count }));
        return;
    }
    if (!confirm(t('roles.confirm_delete', { name }))) return;
    router.delete(`/admin/roles/${role.id}`, { preserveScroll: true });
}
</script>

<template>
    <Head :title="t('roles.title')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="t('roles.title')"
                :description="t('roles.description')"
            />
            <Button as-child>
                <Link href="/admin/roles/create">
                    <Plus class="size-4" />
                    {{ t('roles.create_button') }}
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Card v-for="role in roles" :key="role.id" class="flex flex-col">
                <CardContent class="flex flex-1 flex-col gap-3 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-10 items-center justify-center rounded-xl"
                                :class="badgeClasses(role.color)"
                            >
                                <ShieldCheck class="size-5" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold">{{ roleLabel(role.name, role.display_name) }}</span>
                                    <Lock
                                        v-if="role.is_system"
                                        class="size-3 text-muted-foreground"
                                        :title="t('roles.system_role_locked')"
                                    />
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{ role.name }}
                                </div>
                            </div>
                        </div>
                        <Badge
                            class="rounded-full px-2.5 py-0.5 text-[11px] font-medium"
                            :class="badgeClasses(role.color)"
                        >
                            {{ role.color }}
                        </Badge>
                    </div>

                    <p v-if="role.description" class="text-sm text-muted-foreground">
                        {{ role.description }}
                    </p>

                    <div class="mt-auto flex items-center gap-4 border-t pt-3 text-xs text-muted-foreground">
                        <span>{{ t('roles.users_count', { count: role.user_count }) }}</span>
                        <span>{{ t('roles.permissions_count', { count: role.permission_count }) }}</span>
                    </div>

                    <div class="flex items-center justify-end gap-1.5">
                        <Button as-child variant="outline" size="sm">
                            <Link :href="`/admin/roles/${role.id}/edit`">
                                <Pencil class="size-3.5" />
                                {{ t('roles.edit') }}
                            </Link>
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            class="text-destructive hover:text-destructive"
                            :disabled="role.is_system || role.user_count > 0"
                            @click="destroy(role)"
                        >
                            <Trash2 class="size-3.5" />
                            {{ t('roles.delete') }}
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
