<script setup lang="ts">
/**
 * Service Areas picker. Districts grouped by city. Per-city
 * "Select all / Clear" shortcut so admins covering an entire city
 * don't have to click 12 checkboxes.
 */
import { useForm } from '@inertiajs/vue3';
import { Check, ChevronDown, ChevronRight, Loader2, X } from 'lucide-vue-next';
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

type CityGroup = {
    city_id: number;
    city_name: string;
    districts: { id: number; name: string }[];
};

const props = defineProps<{
    open: boolean;
    companyId: number;
    districtsByCity: CityGroup[];
    selectedDistrictIds: number[];
    locale: string;
    lazyLoaded: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'lazyLoad'): void;
}>();

const de = {
    title: 'Einsatzgebiete bearbeiten',
    description: 'Wählen Sie die Bezirke aus, in denen Ihr Unternehmen tätig ist.',
    empty: 'Keine Städte / Bezirke verfügbar.',
    loading: 'Bezirke werden geladen…',
    selected: (n: number) => `${n} Bezirke ausgewählt`,
    select_all: 'Alle',
    clear: 'Keine',
    save: 'Speichern',
    cancel: 'Abbrechen',
} as const;
const en = {
    title: 'Edit service areas',
    description: 'Pick the districts your business covers.',
    empty: 'No cities / districts available.',
    loading: 'Loading districts…',
    selected: (n: number) => `${n} districts selected`,
    select_all: 'All',
    clear: 'None',
    save: 'Save',
    cancel: 'Cancel',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{ district_ids: number[] }>({
    district_ids: [...props.selectedDistrictIds],
});
const expanded = ref<Set<number>>(new Set());

onMounted(() => {
    if (props.open && !props.lazyLoaded) emit('lazyLoad');
});

watch(
    () => [props.open, props.selectedDistrictIds] as const,
    ([open]) => {
        if (open) {
            form.district_ids = [...props.selectedDistrictIds];
            form.clearErrors();
            if (!props.lazyLoaded) emit('lazyLoad');
            const next = new Set<number>();
            props.districtsByCity.forEach((c) => {
                if (c.districts.some((d) => props.selectedDistrictIds.includes(d.id))) {
                    next.add(c.city_id);
                }
            });
            expanded.value = next;
        }
    },
);

function toggleDistrict(id: number): void {
    form.district_ids = form.district_ids.includes(id)
        ? form.district_ids.filter((x) => x !== id)
        : [...form.district_ids, id];
}

function selectAllInCity(group: CityGroup): void {
    const ids = new Set(form.district_ids);
    group.districts.forEach((d) => ids.add(d.id));
    form.district_ids = Array.from(ids);
}

function clearCity(group: CityGroup): void {
    const strip = new Set(group.districts.map((d) => d.id));
    form.district_ids = form.district_ids.filter((id) => !strip.has(id));
}

function isCityExpanded(cityId: number): boolean {
    return expanded.value.has(cityId);
}

function toggleCity(cityId: number): void {
    const next = new Set(expanded.value);
    next.has(cityId) ? next.delete(cityId) : next.add(cityId);
    expanded.value = next;
}

function citySelectedCount(group: CityGroup): number {
    const set = new Set(form.district_ids);

    return group.districts.filter((d) => set.has(d.id)).length;
}

function close(): void {
    emit('update:open', false);
}

function submit(): void {
    form.post(`/company-portal/${props.companyId}/areas`, {
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
                <p class="text-xs text-[var(--slate)]">
                    {{ t.selected(form.district_ids.length) }}
                </p>

                <div v-if="!lazyLoaded" class="flex items-center justify-center gap-2 rounded-md border border-dashed border-[var(--linen)] p-6 text-sm text-[var(--slate)]">
                    <Loader2 class="size-4 animate-spin" />
                    {{ t.loading }}
                </div>

                <div v-else-if="districtsByCity.length === 0" class="rounded-md border border-dashed border-[var(--linen)] p-6 text-center text-sm text-[var(--slate)]">
                    {{ t.empty }}
                </div>

                <div v-else class="max-h-80 space-y-2 overflow-y-auto rounded-md border border-[var(--linen)] p-2">
                    <div
                        v-for="group in districtsByCity"
                        :key="group.city_id"
                        class="rounded-md border border-[var(--linen)]"
                    >
                        <button
                            type="button"
                            class="flex w-full items-center gap-2 rounded-md p-2 text-left hover:bg-[var(--paper)]"
                            @click="toggleCity(group.city_id)"
                        >
                            <ChevronDown v-if="isCityExpanded(group.city_id)" class="size-4 text-[var(--slate)]" />
                            <ChevronRight v-else class="size-4 text-[var(--slate)]" />
                            <span class="flex-1 text-sm font-semibold">{{ group.city_name }}</span>
                            <span class="text-xs text-[var(--slate)]">
                                {{ citySelectedCount(group) }} / {{ group.districts.length }}
                            </span>
                        </button>

                        <div v-if="isCityExpanded(group.city_id)" class="border-t border-[var(--linen)] p-2">
                            <div class="mb-2 flex gap-1.5">
                                <Button type="button" variant="ghost" size="sm" @click="selectAllInCity(group)">
                                    {{ t.select_all }}
                                </Button>
                                <Button type="button" variant="ghost" size="sm" @click="clearCity(group)">
                                    {{ t.clear }}
                                </Button>
                            </div>
                            <div class="grid grid-cols-2 gap-1">
                                <label
                                    v-for="d in group.districts"
                                    :key="d.id"
                                    class="flex cursor-pointer items-center gap-2 rounded p-1.5 text-xs hover:bg-[var(--paper)]"
                                >
                                    <Checkbox
                                        :model-value="form.district_ids.includes(d.id)"
                                        @update:model-value="toggleDistrict(d.id)"
                                    />
                                    <span class="truncate">{{ d.name }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

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
