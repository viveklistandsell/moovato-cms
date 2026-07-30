<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { ArrowDown, ArrowUp, BadgeCheck, Plus, Search, Trash2 } from 'lucide-vue-next';
import { lookup as lookupCompanies } from '@/routes/admin/companies';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';

type CompanyOption = {
    id: number;
    name: string;
    city: string | null;
    rating_avg: number;
    review_count: number;
    verified: boolean;
    logo_url: string | null;
};

type Settings = {
    mode: 'auto' | 'manual';
    company_ids: number[];
    limit: number;
    only_verified: boolean;
    show_filters: boolean;
    show_sort: boolean;
    items?: unknown;
};

type Data = {
    heading: string;
    subheading: string;
    badge_label: string;
    reviews_label: string;
    recommend_label: string;
    quote_label: string;
    quote_url: string;
    details_label: string;
    empty_text: string;
    count_text: string;
    filters_title: string;
    results_title: string;
    services_title: string;
    rating_title: string;
    features_title: string;
    nearby_title: string;
    nearby_label: string;
    verified_label: string;
    top_rated_label: string;
    no_rating_label: string;
    reset_label: string;
    no_results_text: string;
    sort_label: string;
    sort_relevance_label: string;
    sort_rating_label: string;
    sort_reviews_label: string;
    sort_name_label: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

const search = ref('');
const results = ref<CompanyOption[]>([]);
const picked = ref<CompanyOption[]>([]);
const loading = ref(false);

let timer: ReturnType<typeof setTimeout> | null = null;

async function fetchCompanies(params: Record<string, unknown>): Promise<CompanyOption[]> {
    const response = await fetch(lookupCompanies.url({ query: params }), {
        headers: { Accept: 'application/json' },
    });
    const payload = await response.json();

    return Array.isArray(payload?.companies) ? payload.companies : [];
}

async function runSearch(): Promise<void> {
    loading.value = true;

    try {
        results.value = await fetchCompanies({ q: search.value });
    } catch {
        results.value = [];
    } finally {
        loading.value = false;
    }
}

async function loadPicked(): Promise<void> {
    const ids = settings.value.company_ids ?? [];

    if (ids.length === 0) {
        picked.value = [];

        return;
    }

    const found = await fetchCompanies({ ids });

    picked.value = ids
        .map((id) => found.find((company) => company.id === id))
        .filter((company): company is CompanyOption => Boolean(company));
}

function isPicked(id: number): boolean {
    return (settings.value.company_ids ?? []).includes(id);
}

function add(company: CompanyOption): void {
    if (isPicked(company.id)) {
        return;
    }

    settings.value.company_ids = [...(settings.value.company_ids ?? []), company.id];
    picked.value = [...picked.value, company];
}

function remove(id: number): void {
    settings.value.company_ids = (settings.value.company_ids ?? []).filter(
        (companyId) => companyId !== id,
    );
    picked.value = picked.value.filter((company) => company.id !== id);
}

function move(index: number, offset: number): void {
    const target = index + offset;
    const ids = [...(settings.value.company_ids ?? [])];

    if (target < 0 || target >= ids.length) {
        return;
    }

    [ids[index], ids[target]] = [ids[target], ids[index]];
    settings.value.company_ids = ids;

    const list = [...picked.value];
    [list[index], list[target]] = [list[target], list[index]];
    picked.value = list;
}

watch(search, () => {
    if (timer) {
        clearTimeout(timer);
    }

    timer = setTimeout(() => void runSearch(), 250);
});

onMounted(() => {
    void loadPicked();
    void runSearch();
});
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-3 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Top 10 Umzugsunternehmen in Berlin"
                />
            </div>
            <div class="grid gap-2">
                <Label>Subheading</Label>
                <Textarea v-model="data.subheading" :rows="2" />
            </div>
        </div>

        <div class="space-y-3 rounded-md border bg-muted/30 p-3">
            <Label class="text-sm font-semibold">Company selection</Label>
            <div class="grid gap-3 md:grid-cols-3">
                <div class="grid gap-1">
                    <Label class="text-xs">Mode</Label>
                    <Select
                        :model-value="settings.mode"
                        @update:model-value="
                            (v) => (settings.mode = v as Settings['mode'])
                        "
                    >
                        <SelectTrigger><SelectValue /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="auto">
                                Automatic (best rated first)
                            </SelectItem>
                            <SelectItem value="manual">
                                Manual pick
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Max companies</Label>
                    <Input
                        type="number"
                        min="1"
                        max="30"
                        :model-value="settings.limit"
                        @update:model-value="
                            (v) => (settings.limit = Number(v) || 10)
                        "
                    />
                </div>
                <div class="flex items-end gap-2 pb-2">
                    <Switch
                        :model-value="settings.only_verified"
                        @update:model-value="
                            (v) => (settings.only_verified = Boolean(v))
                        "
                    />
                    <Label class="text-xs">Verified only</Label>
                </div>
            </div>

            <div class="flex flex-wrap gap-6">
                <div class="flex items-center gap-2">
                    <Switch
                        :model-value="settings.show_filters !== false"
                        @update:model-value="
                            (v) => (settings.show_filters = Boolean(v))
                        "
                    />
                    <Label class="text-xs">Show filter sidebar</Label>
                </div>
                <div class="flex items-center gap-2">
                    <Switch
                        :model-value="settings.show_sort !== false"
                        @update:model-value="
                            (v) => (settings.show_sort = Boolean(v))
                        "
                    />
                    <Label class="text-xs">Show sort dropdown</Label>
                </div>
            </div>

            <template v-if="settings.mode === 'manual'">
                <div class="grid gap-1">
                    <Label class="text-xs">Search companies</Label>
                    <div class="relative">
                        <Search
                            class="absolute top-2.5 left-2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            class="pl-8"
                            placeholder="Search by company name…"
                        />
                    </div>
                </div>

                <div class="max-h-56 space-y-1 overflow-y-auto rounded-md border bg-background p-1">
                    <p
                        v-if="loading"
                        class="p-2 text-xs text-muted-foreground"
                    >
                        Loading…
                    </p>
                    <p
                        v-else-if="results.length === 0"
                        class="p-2 text-xs text-muted-foreground"
                    >
                        No companies found.
                    </p>
                    <div
                        v-for="company in results"
                        :key="company.id"
                        class="flex items-center justify-between gap-2 rounded px-2 py-1.5 hover:bg-muted"
                    >
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium">
                                {{ company.name }}
                                <BadgeCheck
                                    v-if="company.verified"
                                    class="inline size-3.5 text-primary"
                                />
                            </p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ company.city }} · {{ company.rating_avg }} ({{
                                    company.review_count
                                }})
                            </p>
                        </div>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            :disabled="isPicked(company.id)"
                            @click="add(company)"
                        >
                            <Plus class="size-4" />
                        </Button>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label class="text-xs font-semibold">
                        Selected ({{ picked.length }})
                    </Label>
                    <p
                        v-if="picked.length === 0"
                        class="text-xs text-muted-foreground"
                    >
                        Nothing picked yet — the section stays empty in manual
                        mode until you add companies.
                    </p>
                    <div
                        v-for="(company, i) in picked"
                        :key="company.id"
                        class="flex items-center gap-2 rounded-md border bg-background px-2 py-1.5"
                    >
                        <span class="w-5 text-xs text-muted-foreground">
                            {{ i + 1 }}.
                        </span>
                        <span class="min-w-0 flex-1 truncate text-sm">
                            {{ company.name }}
                        </span>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            :disabled="i === 0"
                            @click="move(i, -1)"
                        >
                            <ArrowUp class="size-4" />
                        </Button>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            :disabled="i === picked.length - 1"
                            @click="move(i, 1)"
                        >
                            <ArrowDown class="size-4" />
                        </Button>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="remove(company.id)"
                        >
                            <Trash2 class="size-4 text-destructive" />
                        </Button>
                    </div>
                </div>
            </template>
        </div>

        <div class="space-y-3 rounded-md border bg-muted/30 p-3">
            <Label class="text-sm font-semibold">Card labels</Label>
            <div class="grid gap-2 md:grid-cols-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Top badge</Label>
                    <Input
                        v-model="data.badge_label"
                        placeholder="Bestbewertetes Umzugsunternehmen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Reviews label</Label>
                    <Input
                        v-model="data.reviews_label"
                        placeholder="Bewertungen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Recommend label</Label>
                    <Input
                        v-model="data.recommend_label"
                        placeholder="Weiterempfehlung"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Details label</Label>
                    <Input v-model="data.details_label" placeholder="Details" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Quote button</Label>
                    <Input
                        v-model="data.quote_label"
                        placeholder="Angebot anfordern"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Quote URL</Label>
                    <Input v-model="data.quote_url" placeholder="#" />
                </div>
                <div class="grid gap-1 md:col-span-2">
                    <Label class="text-xs">Empty state text</Label>
                    <Textarea v-model="data.empty_text" :rows="2" />
                </div>
            </div>
        </div>

        <div class="space-y-3 rounded-md border bg-muted/30 p-3">
            <Label class="text-sm font-semibold">Filters &amp; sorting</Label>
            <div class="grid gap-2 md:grid-cols-2">
                <div class="grid gap-1">
                    <Label class="text-xs">
                        Result count ({count} = number)
                    </Label>
                    <Input
                        v-model="data.count_text"
                        placeholder="{count} Umzugsunternehmen gefunden"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Filters title</Label>
                    <Input v-model="data.filters_title" placeholder="Filter" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Results title</Label>
                    <Input
                        v-model="data.results_title"
                        placeholder="Ergebnisse"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Services group</Label>
                    <Input
                        v-model="data.services_title"
                        placeholder="Dienstleistungen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Rating group</Label>
                    <Input
                        v-model="data.rating_title"
                        placeholder="Bewertung"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Features group</Label>
                    <Input
                        v-model="data.features_title"
                        placeholder="Merkmale"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Verified filter</Label>
                    <Input
                        v-model="data.verified_label"
                        placeholder="Verifiziert"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Top rated filter</Label>
                    <Input
                        v-model="data.top_rated_label"
                        placeholder="Top bewertet"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">No-rating filter</Label>
                    <Input
                        v-model="data.no_rating_label"
                        placeholder="Keine Bewertungen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Nearby group</Label>
                    <Input
                        v-model="data.nearby_title"
                        placeholder="In der Nähe"
                    />
                </div>
                <div class="grid gap-1 md:col-span-2">
                    <Label class="text-xs">
                        Nearby filter ({city} = city name)
                    </Label>
                    <Input
                        v-model="data.nearby_label"
                        placeholder="Zeige Unternehmen in der Nähe von {city}"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Reset link</Label>
                    <Input
                        v-model="data.reset_label"
                        placeholder="Filter zurücksetzen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Sort label</Label>
                    <Input
                        v-model="data.sort_label"
                        placeholder="Sortieren nach:"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Sort: relevance</Label>
                    <Input
                        v-model="data.sort_relevance_label"
                        placeholder="Am relevantesten"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Sort: rating</Label>
                    <Input
                        v-model="data.sort_rating_label"
                        placeholder="Beste Bewertung"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Sort: reviews</Label>
                    <Input
                        v-model="data.sort_reviews_label"
                        placeholder="Meiste Bewertungen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Sort: name</Label>
                    <Input
                        v-model="data.sort_name_label"
                        placeholder="Name A–Z"
                    />
                </div>
                <div class="grid gap-1 md:col-span-2">
                    <Label class="text-xs">No results text</Label>
                    <Textarea v-model="data.no_results_text" :rows="2" />
                </div>
            </div>
        </div>
    </div>
</template>
