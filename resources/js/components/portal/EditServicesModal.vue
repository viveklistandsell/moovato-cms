<script setup lang="ts">
/**
 * Business Categories (services) picker. Flat checkbox list with a
 * search filter, parent-category name shown as a small chip on each
 * row so users can scan quickly.
 *
 * Available services + currently-selected ids are fetched via
 * Inertia lazy props when this modal mounts (see `router.reload`
 * below).
 */
import { router, useForm } from '@inertiajs/vue3';
import { Check, Loader2, Search, X } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';

type ServiceOption = { id: number; name: string; parent_name: string | null };

const props = defineProps<{
    open: boolean;
    companyId: number;
    availableServices: ServiceOption[];
    selectedServiceIds: number[];
    locale: string;
    lazyLoaded: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'lazyLoad'): void;
}>();

const de = {
    title: 'Leistungskategorien bearbeiten',
    description: 'Wählen Sie alle Kategorien aus, die zu Ihrem Unternehmen passen.',
    search_placeholder: 'Kategorie suchen…',
    empty: 'Keine Kategorien gefunden.',
    loading: 'Kategorien werden geladen…',
    selected: (n: number) => `${n} ausgewählt`,
    save: 'Speichern',
    cancel: 'Abbrechen',
} as const;
const en = {
    title: 'Edit business categories',
    description: 'Pick every category that describes your business.',
    search_placeholder: 'Search category…',
    empty: 'No categories match.',
    loading: 'Loading categories…',
    selected: (n: number) => `${n} selected`,
    save: 'Save',
    cancel: 'Cancel',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{ service_ids: number[] }>({
    service_ids: [...props.selectedServiceIds],
});
const search = ref('');

onMounted(() => {
    if (props.open && !props.lazyLoaded) {
        emit('lazyLoad');
    }
});

watch(
    () => [props.open, props.selectedServiceIds] as const,
    ([open]) => {
        if (open) {
            form.service_ids = [...props.selectedServiceIds];
            form.clearErrors();
            if (!props.lazyLoaded) emit('lazyLoad');
        }
    },
);

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (q === '') return props.availableServices;

    return props.availableServices.filter((s) => {
        return s.name.toLowerCase().includes(q)
            || (s.parent_name ?? '').toLowerCase().includes(q);
    });
});

function toggle(id: number): void {
    form.service_ids = form.service_ids.includes(id)
        ? form.service_ids.filter((x) => x !== id)
        : [...form.service_ids, id];
}

function close(): void {
    emit('update:open', false);
}

function submit(): void {
    form.post(`/company-portal/${props.companyId}/services`, {
        preserveScroll: true,
        onSuccess: () => close(),
    });
}
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ t.title }}</DialogTitle>
                <DialogDescription>{{ t.description }}</DialogDescription>
            </DialogHeader>

            <form class="space-y-3" @submit.prevent="submit">
                <div class="relative">
                    <Search class="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-[var(--slate)]" />
                    <Input
                        v-model="search"
                        :placeholder="t.search_placeholder"
                        class="pl-8"
                    />
                </div>

                <p class="text-xs text-[var(--slate)]">
                    {{ t.selected(form.service_ids.length) }}
                </p>

                <div v-if="!lazyLoaded" class="flex items-center justify-center gap-2 rounded-md border border-dashed border-[var(--linen)] p-6 text-sm text-[var(--slate)]">
                    <Loader2 class="size-4 animate-spin" />
                    {{ t.loading }}
                </div>

                <div v-else-if="filtered.length === 0" class="rounded-md border border-dashed border-[var(--linen)] p-6 text-center text-sm text-[var(--slate)]">
                    {{ t.empty }}
                </div>

                <ul v-else class="max-h-72 divide-y divide-[var(--linen)] overflow-y-auto rounded-md border border-[var(--linen)]">
                    <li
                        v-for="s in filtered"
                        :key="s.id"
                        class="flex items-center gap-3 p-2.5 transition-colors hover:bg-[var(--paper)]"
                    >
                        <Checkbox
                            :model-value="form.service_ids.includes(s.id)"
                            @update:model-value="toggle(s.id)"
                        />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm">{{ s.name }}</p>
                            <p v-if="s.parent_name" class="mt-0.5 truncate text-[10px] text-[var(--slate)]">
                                {{ s.parent_name }}
                            </p>
                        </div>
                    </li>
                </ul>

                <DialogFooter>
                    <Button type="button" variant="ghost" @click="close">
                        <X class="size-4" />
                        {{ t.cancel }}
                    </Button>
                    <Button type="submit" :disabled="form.processing || !lazyLoaded">
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <Check v-else class="size-4" />
                        {{ t.save }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
