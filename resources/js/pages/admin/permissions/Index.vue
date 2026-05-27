<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { KeyRound, Pencil, ShieldCheck } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

type RoleRef = {
    id: number;
    name: string;
    display_name: string;
    color: string;
};

type PermissionItem = {
    id: number;
    name: string;
    display_name: string;
    description: string | null;
    roles: RoleRef[];
};

type PermissionGroup = {
    group: string;
    items: PermissionItem[];
};

defineProps<{
    groups: PermissionGroup[];
    roles: RoleRef[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Users', href: '/admin/users' },
            { title: 'Permissions', href: '/admin/permissions' },
        ],
    },
});

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
</script>

<template>
    <Head title="Permissions" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Permissions"
                description="Every permission in the system, bucketed by module. Edit who has them via the Roles page."
            />
            <Button as-child variant="outline">
                <Link href="/admin/roles">
                    <ShieldCheck class="size-4" />
                    Manage roles
                </Link>
            </Button>
        </div>

        <div class="space-y-6">
            <Card v-for="group in groups" :key="group.group">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <KeyRound class="size-4 text-muted-foreground" />
                        {{ group.group }}
                        <Badge variant="secondary" class="text-[10px]">
                            {{ group.items.length }}
                        </Badge>
                    </CardTitle>
                    <CardDescription>
                        Permissions in the {{ group.group }} module.
                    </CardDescription>
                </CardHeader>
                <CardContent class="overflow-x-auto p-0">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 text-left text-xs">
                            <tr>
                                <th class="px-4 py-2 font-medium uppercase tracking-wide text-muted-foreground">
                                    Permission
                                </th>
                                <th class="px-4 py-2 font-medium uppercase tracking-wide text-muted-foreground">
                                    Key
                                </th>
                                <th class="px-4 py-2 font-medium uppercase tracking-wide text-muted-foreground">
                                    Assigned to
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="perm in group.items"
                                :key="perm.id"
                                class="border-t"
                            >
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ perm.display_name }}</div>
                                    <div
                                        v-if="perm.description"
                                        class="text-xs text-muted-foreground"
                                    >
                                        {{ perm.description }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <code class="rounded bg-muted px-1.5 py-0.5 text-[11px]">
                                        {{ perm.name }}
                                    </code>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <Badge
                                            v-for="role in perm.roles"
                                            :key="role.id"
                                            class="rounded-full px-2 py-0.5 text-[10px]"
                                            :class="badgeClasses(role.color)"
                                        >
                                            {{ role.display_name }}
                                        </Badge>
                                        <span
                                            v-if="perm.roles.length === 0"
                                            class="text-xs text-muted-foreground"
                                        >
                                            — unused —
                                        </span>
                                        <Button
                                            v-if="perm.roles.length > 0"
                                            as-child
                                            variant="ghost"
                                            size="sm"
                                            class="h-6 px-2 text-[10px]"
                                        >
                                            <Link :href="`/admin/roles/${perm.roles[0].id}/edit`">
                                                <Pencil class="size-3" />
                                            </Link>
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
