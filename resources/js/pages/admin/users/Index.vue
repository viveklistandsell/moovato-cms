<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    Camera,
    Filter,
    Key,
    Pencil,
    Plus,
    RotateCcw,
    ShieldCheck,
    Trash2,
    UserCheck,
    Users,
    UserX,
    X,
} from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import BulkActions, {
    type BulkAction,
} from '@/components/common/BulkActions.vue';
import Pagination, {
    type PaginationMeta,
} from '@/components/common/Pagination.vue';
import PerPageSelect from '@/components/common/PerPageSelect.vue';
import SearchInput from '@/components/common/SearchInput.vue';
import SortableColumn from '@/components/common/SortableColumn.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
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
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import InputError from '@/components/InputError.vue';
import MediaPicker from '@/components/common/MediaPicker.vue';
import { useRowSelection } from '@/composables/common/useRowSelection';
import { useTableQuery } from '@/composables/common/useTableQuery';

type RoleOption = { value: string; label: string; color: string };
type StatusOption = { value: string; label: string; color: string };

type UserRow = {
    id: number;
    name: string;
    email: string;
    avatar: string | null;
    avatar_url: string | null;
    status: 'active' | 'inactive' | 'banned' | 'pending' | null;
    status_label: string | null;
    status_color: string | null;
    role: string | null;
    role_display_name: string | null;
    role_color: string | null;
    last_login_at: string | null;
    last_login_ip: string | null;
    created_at: string | null;
};

type Stats = { total: number; active: number; inactive: number; admins: number };

type Filters = {
    q: string | null;
    sort_by: string | null;
    sort_dir: 'asc' | 'desc';
    per_page: number;
    status: string;
    role: string;
};

const props = defineProps<{
    users: UserRow[];
    roleOptions: RoleOption[];
    statusOptions: StatusOption[];
    stats: Stats;
    currentUserId: number | null;
    filters: Filters;
    pagination: PaginationMeta;
    // Sent by the dedicated /admin/users/create route — tells the page to
    // auto-open the create drawer without the legacy `?new=1` hack.
    openCreate?: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Users', href: '/admin/users' },
        ],
    },
});

// ---------- table query ----------
const {
    search,
    sortBy,
    sortDir,
    perPage,
    isLoading,
    setSearch,
    toggleSort,
    setPerPage,
} = useTableQuery(
    '/admin/users',
    {
        q: props.filters.q,
        sort_by: props.filters.sort_by,
        sort_dir: props.filters.sort_dir,
        per_page: props.filters.per_page,
    },
    { only: ['users', 'pagination', 'filters', 'stats'] },
);

const statusFilter = ref<string>(props.filters.status);
const roleFilter = ref<string>(props.filters.role);

function applyFilters(): void {
    const params: Record<string, string | number> = {};
    if (search.value) params.q = search.value;
    if (sortBy.value) {
        params.sort_by = sortBy.value;
        params.sort_dir = sortDir.value;
    }
    if (perPage.value && perPage.value !== 10) params.per_page = perPage.value;
    if (statusFilter.value !== 'all') params.status = statusFilter.value;
    if (roleFilter.value !== 'all') params.role = roleFilter.value;

    router.get('/admin/users', params, {
        only: ['users', 'pagination', 'filters', 'stats'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function resetAllFilters(): void {
    search.value = '';
    sortBy.value = null;
    sortDir.value = 'desc';
    perPage.value = 10;
    statusFilter.value = 'all';
    roleFilter.value = 'all';
    router.get('/admin/users', {}, {
        only: ['users', 'pagination', 'filters', 'stats'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

const isFiltered = computed(
    () =>
        (search.value && search.value.length > 0) ||
        sortBy.value !== null ||
        statusFilter.value !== 'all' ||
        roleFilter.value !== 'all',
);

// ---------- selection + bulk actions ----------
const selection = useRowSelection();
const visibleIds = computed(() => props.users.map((u) => u.id));
const allOnPageSelected = computed(() => selection.areAllSelected(visibleIds.value));
const someOnPageSelected = computed(
    () => !allOnPageSelected.value && selection.someSelected(visibleIds.value),
);

const bulkActions: BulkAction[] = [
    { value: 'activate', label: 'Activate' },
    { value: 'deactivate', label: 'Deactivate' },
    { value: 'ban', label: 'Ban' },
    {
        value: 'delete',
        label: 'Delete',
        destructive: true,
        confirm: 'Delete {count} selected user(s)?',
    },
];

function applyBulkAction(action: string): void {
    if (selection.isEmpty.value) return;
    router.post(
        '/admin/users/bulk-action',
        { action, ids: selection.ids.value },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => selection.clear(),
        },
    );
}

// ---------- stats cards ----------
const statsCards = computed(() => [
    {
        key: 'total',
        label: 'Total Users',
        value: props.stats.total,
        sub: 'All Registered Users',
        icon: Users,
        iconBg: 'bg-sky-100 text-sky-600 dark:bg-sky-900/40 dark:text-sky-300',
    },
    {
        key: 'active',
        label: 'Active Users',
        value: props.stats.active,
        sub: 'Users Active',
        icon: UserCheck,
        iconBg: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300',
    },
    {
        key: 'inactive',
        label: 'Inactive Users',
        value: props.stats.inactive,
        sub: 'Users Inactive',
        icon: UserX,
        iconBg: 'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300',
    },
    {
        key: 'admins',
        label: 'Admins',
        value: props.stats.admins,
        sub: 'Total Admins',
        icon: ShieldCheck,
        iconBg: 'bg-violet-100 text-violet-600 dark:bg-violet-900/40 dark:text-violet-300',
    },
]);

// ---------- color + formatting helpers ----------
function badgeClasses(color: string | null | undefined): string {
    const map: Record<string, string> = {
        rose: 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
        amber: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        emerald: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        sky: 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
        violet: 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300',
        neutral: 'bg-neutral-200 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
    };
    return map[color ?? 'neutral'] ?? map.neutral;
}

function relativeTime(iso: string | null): string {
    if (!iso) return 'Never';
    const diffMs = Date.now() - new Date(iso).getTime();
    const min = Math.floor(diffMs / 60_000);
    if (min < 1) return 'just now';
    if (min < 60) return `${min} min ago`;
    const hr = Math.floor(min / 60);
    if (hr < 24) return `${hr} hour${hr === 1 ? '' : 's'} ago`;
    const day = Math.floor(hr / 24);
    return `${day} day${day === 1 ? '' : 's'} ago`;
}

function fullTime(iso: string | null): string {
    if (!iso) return '';
    const d = new Date(iso);
    return d.toLocaleString('en-US', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function initials(name: string): string {
    return name
        .split(/\s+/)
        .map((p) => p.charAt(0).toUpperCase())
        .slice(0, 2)
        .join('');
}

// ---------- drawer (create / edit) ----------
type DrawerMode = 'create' | 'edit';
const drawerOpen = ref(false);
const drawerMode = ref<DrawerMode>('create');
const editingUser = ref<UserRow | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    status: 'active',
    role: '',
    avatar: null as File | null,
    avatar_path: '',
    remove_avatar: false,
});

const avatarPreview = ref<string | null>(null);
const avatarPickerOpen = ref(false);

function openCreate(): void {
    drawerMode.value = 'create';
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    form.status = 'active';
    avatarPreview.value = null;
    drawerOpen.value = true;
}

function openEdit(row: UserRow): void {
    drawerMode.value = 'edit';
    editingUser.value = row;
    form.reset();
    form.clearErrors();
    form.name = row.name;
    form.email = row.email;
    form.status = row.status ?? 'active';
    form.role = row.role ?? '';
    form.avatar_path = row.avatar ?? '';
    avatarPreview.value = row.avatar_url;
    drawerOpen.value = true;
}

function onAvatarPicked(file: {
    path: string;
    url: string;
    name: string;
    thumb_path?: string | null;
    thumb_url?: string | null;
}): void {
    form.avatar = null;
    form.avatar_path = file.thumb_path && file.thumb_path !== '' ? file.thumb_path : file.path;
    form.remove_avatar = false;
    avatarPreview.value = file.thumb_url && file.thumb_url !== '' ? file.thumb_url : file.url;
}

function removeAvatar(): void {
    form.avatar = null;
    form.avatar_path = '';
    form.remove_avatar = true;
    avatarPreview.value = null;
}

function onAvatarLoadError(): void {
    avatarPreview.value = null;
}

function submitDrawer(): void {
    if (drawerMode.value === 'create') {
        form.post('/admin/users', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                drawerOpen.value = false;
            },
        });
        return;
    }
    if (editingUser.value === null) return;
    form
        .transform((data) => ({ ...data, _method: 'put' }))
        .post(`/admin/users/${editingUser.value.id}`, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                drawerOpen.value = false;
            },
        });
}

// ---------- reset password ----------
const resetOpen = ref(false);
const resetTarget = ref<UserRow | null>(null);
const resetForm = useForm({ password: '', password_confirmation: '' });

function openReset(row: UserRow): void {
    resetTarget.value = row;
    resetForm.reset();
    resetForm.clearErrors();
    resetOpen.value = true;
}

function submitReset(): void {
    if (resetTarget.value === null) return;
    resetForm.post(`/admin/users/${resetTarget.value.id}/reset-password`, {
        preserveScroll: true,
        onSuccess: () => {
            resetOpen.value = false;
        },
    });
}

// ---------- single delete ----------
function destroyUser(row: UserRow): void {
    if (!confirm(`Delete user "${row.name}"?`)) return;
    router.delete(`/admin/users/${row.id}`, {
        preserveScroll: true,
    });
}

// Reset row selection if the page data changes underneath us (filters etc).
watch(() => props.users, () => selection.clear());

const arrivedViaCreateRoute = ref(false);

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const fromQueryParam = params.get('new') === '1';

    if (props.openCreate || fromQueryParam) {
        arrivedViaCreateRoute.value = true;
        openCreate();
    }

    if (fromQueryParam) {
        params.delete('new');
        const query = params.toString();
        const url = window.location.pathname + (query !== '' ? `?${query}` : '');
        window.history.replaceState({}, '', url);
    }
});

watch(drawerOpen, (isOpen) => {
    if (
        !isOpen &&
        arrivedViaCreateRoute.value &&
        window.location.pathname.endsWith('/admin/users/create')
    ) {
        window.history.replaceState({}, '', '/admin/users');
        arrivedViaCreateRoute.value = false;
    }
});
</script>

<template>
    <Head title="User Management" />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="User Management"
                description="Manage users, roles, and access."
            />
            <Button @click="openCreate">
                <Plus class="size-4" />
                Add New User
            </Button>
        </div>

        <!-- Stats cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card v-for="card in statsCards" :key="card.key" class="overflow-hidden">
                <CardContent class="flex items-center gap-4 p-5">
                    <div
                        class="flex size-12 shrink-0 items-center justify-center rounded-xl"
                        :class="card.iconBg"
                    >
                        <component :is="card.icon" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-medium uppercase tracking-wide text-muted-foreground">
                            {{ card.label }}
                        </div>
                        <div class="text-3xl font-bold tracking-tight">
                            {{ card.value }}
                        </div>
                        <div class="text-xs text-muted-foreground">
                            {{ card.sub }}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Filter bar + table -->
        <Card>
            <CardContent class="space-y-4 p-4">
                <div class="flex flex-wrap items-center gap-3">
                    <SearchInput
                        :model-value="search"
                        :loading="isLoading"
                        placeholder="Search by name or email…"
                        @search="setSearch"
                    />
                    <Select
                        :model-value="roleFilter"
                        @update:model-value="(v) => { roleFilter = v as string; applyFilters(); }"
                    >
                        <SelectTrigger class="h-9 w-44">
                            <SelectValue placeholder="All Roles" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Roles</SelectItem>
                            <SelectItem
                                v-for="opt in roleOptions"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                {{ opt.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select
                        :model-value="statusFilter"
                        @update:model-value="(v) => { statusFilter = v as string; applyFilters(); }"
                    >
                        <SelectTrigger class="h-9 w-40">
                            <SelectValue placeholder="All Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Status</SelectItem>
                            <SelectItem
                                v-for="opt in statusOptions"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                {{ opt.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Button type="button" @click="applyFilters">
                        <Filter class="size-4" />
                        Filter
                    </Button>

                    <div class="ml-auto flex items-center gap-2">
                        <BulkActions
                            :actions="bulkActions"
                            :count="selection.count.value"
                            label="Bulk action"
                            @action="applyBulkAction"
                        />
                        <PerPageSelect
                            :model-value="perPage"
                            :options="[10, 25, 50, 100]"
                            label="Per page"
                            @update:model-value="setPerPage"
                        />
                        <Button
                            v-if="isFiltered"
                            variant="outline"
                            @click="resetAllFilters"
                        >
                            <RotateCcw class="size-4" />
                            Reset
                        </Button>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 text-left text-xs">
                            <tr>
                                <th class="w-10 px-3 py-3">
                                    <Checkbox
                                        :model-value="
                                            allOnPageSelected
                                                ? true
                                                : someOnPageSelected
                                                  ? 'indeterminate'
                                                  : false
                                        "
                                        aria-label="Select all on this page"
                                        @update:model-value="selection.toggleAll(visibleIds)"
                                    />
                                </th>
                                <th class="px-3 py-3">
                                    <SortableColumn
                                        column="id"
                                        label="ID"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="name"
                                        label="User"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="email"
                                        label="Email"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3 font-medium uppercase tracking-wide text-muted-foreground">
                                    Role
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="status"
                                        label="Status"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="last_login_at"
                                        label="Last Login"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3 text-center font-medium uppercase tracking-wide text-muted-foreground">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="users.length === 0">
                                <td colspan="8" class="px-4 py-12 text-center text-sm text-muted-foreground">
                                    {{ isFiltered ? 'No users match your filters.' : 'No users yet.' }}
                                </td>
                            </tr>
                            <tr
                                v-for="row in users"
                                :key="row.id"
                                class="border-t transition-colors hover:bg-muted/30"
                            >
                                <td class="px-3 py-3">
                                    <Checkbox
                                        :model-value="selection.isSelected(row.id)"
                                        :aria-label="`Select ${row.name}`"
                                        @update:model-value="selection.toggle(row.id)"
                                    />
                                </td>
                                <td class="px-3 py-3 text-xs text-muted-foreground">
                                    {{ row.id }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-muted text-xs font-semibold text-muted-foreground">
                                            <img
                                                v-if="row.avatar_url"
                                                :src="row.avatar_url"
                                                :alt="row.name"
                                                class="size-full object-cover"
                                            />
                                            <span v-else>{{ initials(row.name) }}</span>
                                        </div>
                                        <div class="font-medium">{{ row.name }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-muted-foreground">
                                    {{ row.email }}
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        v-if="row.role"
                                        class="rounded-full px-2.5 py-0.5 text-[11px] font-medium"
                                        :class="badgeClasses(row.role_color)"
                                    >
                                        {{ row.role_display_name }}
                                    </Badge>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        v-if="row.status"
                                        class="rounded-full px-2.5 py-0.5 text-[11px] font-medium"
                                        :class="badgeClasses(row.status_color)"
                                    >
                                        {{ row.status_label }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs">
                                        {{ relativeTime(row.last_login_at) }}
                                    </div>
                                    <div class="text-[12px] text-muted-foreground">
                                        {{ fullTime(row.last_login_at) }}
                                    </div>
                                    <div
                                        v-if="row.last_login_ip"
                                        class="mt-0.5 font-mono text-[12px] text-muted-foreground"
                                        title="Last login IP"
                                    >
                                        {{ row.last_login_ip }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <Button
                                            type="button"
                                            size="icon"
                                            class="size-8 bg-sky-500 hover:bg-sky-600"
                                            :title="`Edit ${row.name}`"
                                            @click="openEdit(row)"
                                        >
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button
                                            type="button"
                                            size="icon"
                                            class="size-8 bg-amber-500 hover:bg-amber-600"
                                            :title="`Reset password for ${row.name}`"
                                            @click="openReset(row)"
                                        >
                                            <Key class="size-4" />
                                        </Button>
                                        <Button
                                            type="button"
                                            size="icon"
                                            class="size-8 bg-rose-500 hover:bg-rose-600"
                                            :title="`Delete ${row.name}`"
                                            :disabled="row.id === currentUserId"
                                            @click="destroyUser(row)"
                                        >
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination
                    :pagination="pagination"
                    :only="['users', 'pagination', 'filters', 'stats']"
                />
            </CardContent>
        </Card>

        <!-- Add / Edit drawer -->
        <Sheet v-model:open="drawerOpen">
            <SheetContent side="right" class="flex w-full flex-col gap-0 overflow-hidden p-0 sm:max-w-md">
                <SheetHeader class="border-b px-6 py-4">
                    <SheetTitle>
                        {{ drawerMode === 'create' ? 'Add New User' : `Edit ${editingUser?.name ?? 'User'}` }}
                    </SheetTitle>
                    <SheetDescription>
                        {{ drawerMode === 'create'
                            ? 'Create a new user with a role and starting password.'
                            : 'Update the user. Leave password fields empty to keep the current password.' }}
                    </SheetDescription>
                </SheetHeader>

                <form class="flex-1 space-y-4 overflow-y-auto px-6 py-5" @submit.prevent="submitDrawer">
                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            class="group relative flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-full border border-border bg-muted text-base font-semibold text-muted-foreground transition-colors hover:border-primary"
                            :title="avatarPreview ? 'Change avatar' : 'Upload avatar'"
                            @click="avatarPickerOpen = true"
                        >
                            <img
                                v-if="avatarPreview"
                                :src="avatarPreview"
                                alt=""
                                class="size-full object-cover"
                                @error="onAvatarLoadError"
                            />
                            <span v-else>{{ initials(form.name || '?') }}</span>
                            <span
                                class="absolute inset-0 flex items-center justify-center bg-black/40 text-white opacity-0 transition-opacity group-hover:opacity-100"
                            >
                                <Camera class="size-5" />
                            </span>
                        </button>
                        <div class="flex flex-col gap-1">
                            <button
                                type="button"
                                class="text-left text-xs font-medium text-primary hover:underline"
                                @click="avatarPickerOpen = true"
                            >
                                {{ avatarPreview ? 'Change avatar' : 'Upload avatar' }}
                            </button>
                            <button
                                v-if="avatarPreview"
                                type="button"
                                class="text-left text-xs text-muted-foreground hover:text-destructive"
                                @click="removeAvatar"
                            >
                                Remove
                            </button>
                            <p class="text-[11px] text-muted-foreground">
                                Pick an image from the media library.
                            </p>
                            <InputError :message="form.errors.avatar" />
                            <InputError :message="form.errors.avatar_path" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="user-name">Name</Label>
                        <Input id="user-name" v-model="form.name" placeholder="Enter full name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="user-email">Email</Label>
                        <Input
                            id="user-email"
                            v-model="form.email"
                            type="email"
                            placeholder="Enter email address"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="user-role">Role</Label>
                        <Select
                            :model-value="form.role"
                            @update:model-value="(v) => (form.role = v as string)"
                        >
                            <SelectTrigger id="user-role">
                                <SelectValue placeholder="Select Role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="opt in roleOptions"
                                    :key="opt.value"
                                    :value="opt.value"
                                >
                                    {{ opt.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.role" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="user-password">
                            Password
                            <span v-if="drawerMode === 'edit'" class="text-xs text-muted-foreground">
                                (leave empty to keep current)
                            </span>
                        </Label>
                        <Input
                            id="user-password"
                            v-model="form.password"
                            type="password"
                            placeholder="Enter password"
                            autocomplete="new-password"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="user-password-confirm">Confirm Password</Label>
                        <Input
                            id="user-password-confirm"
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="Confirm password"
                            autocomplete="new-password"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="user-status">Status</Label>
                        <Select
                            :model-value="form.status"
                            @update:model-value="(v) => (form.status = v as string)"
                        >
                            <SelectTrigger id="user-status">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="opt in statusOptions"
                                    :key="opt.value"
                                    :value="opt.value"
                                >
                                    {{ opt.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.status" />
                    </div>
                </form>

                <SheetFooter class="flex flex-row items-center justify-end gap-2 border-t bg-muted/30 px-6 py-4">
                    <Button type="button" variant="outline" @click="drawerOpen = false">
                        <X class="size-4" />
                        Cancel
                    </Button>
                    <Button type="button" :disabled="form.processing" @click="submitDrawer">
                        {{ drawerMode === 'create' ? 'Save User' : 'Save Changes' }}
                    </Button>
                </SheetFooter>
            </SheetContent>
        </Sheet>

        <!-- Reset password drawer -->
        <Sheet v-model:open="resetOpen">
            <SheetContent side="right" class="flex w-full flex-col gap-0 overflow-hidden p-0 sm:max-w-sm">
                <SheetHeader class="border-b px-6 py-4">
                    <SheetTitle>Reset password</SheetTitle>
                    <SheetDescription>
                        Set a new password for {{ resetTarget?.name ?? 'this user' }}.
                    </SheetDescription>
                </SheetHeader>

                <form class="flex-1 space-y-4 px-6 py-5" @submit.prevent="submitReset">
                    <div class="grid gap-2">
                        <Label for="reset-password">New password</Label>
                        <Input
                            id="reset-password"
                            v-model="resetForm.password"
                            type="password"
                            autocomplete="new-password"
                        />
                        <InputError :message="resetForm.errors.password" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="reset-password-confirm">Confirm new password</Label>
                        <Input
                            id="reset-password-confirm"
                            v-model="resetForm.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                        />
                    </div>
                </form>

                <SheetFooter class="flex flex-row items-center justify-end gap-2 border-t bg-muted/30 px-6 py-4">
                    <Button type="button" variant="outline" @click="resetOpen = false">
                        Cancel
                    </Button>
                    <Button type="button" :disabled="resetForm.processing" @click="submitReset">
                        Reset password
                    </Button>
                </SheetFooter>
            </SheetContent>
        </Sheet>

        <!-- Avatar picker: opens from the user drawer; image-only listing. -->
        <MediaPicker v-model:open="avatarPickerOpen" accept="image" @pick="onAvatarPicked" />
    </div>
</template>
