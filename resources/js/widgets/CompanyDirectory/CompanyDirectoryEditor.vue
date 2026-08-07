<script setup lang="ts">
import { ChevronDown, ChevronUp, Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Company = {
    logo_path: string | null;
    logo_url: string | null;
    name: string;
    ribbon_label: string | null;
    score: string;
    reviews_count: number;
    pro_reviews_count: number;
    description: string;
    address: string;
    services: string;
    pros: string;
    cons: string;
    quote_url: string;
};

type Settings = {
    companies: Company[];
};

type Data = {
    heading: string;
    subheading: string;
    all_label: string;
    score_label: string;
    pro_reviews_label: string;
    rating_prefix: string;
    pros_heading: string;
    cons_heading: string;
    quote_label: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addCompany(): void {
    settings.value.companies = [
        ...(settings.value.companies ?? []),
        {
            logo_path: null,
            logo_url: null,
            name: '',
            ribbon_label: null,
            score: '',
            reviews_count: 0,
            pro_reviews_count: 0,
            description: '',
            address: '',
            services: '',
            pros: '',
            cons: '',
            quote_url: '#',
        },
    ];
}

function removeCompany(index: number): void {
    settings.value.companies = (settings.value.companies ?? []).filter(
        (_, i) => i !== index,
    );
}

function move(index: number, delta: number): void {
    const list = [...(settings.value.companies ?? [])];
    const target = index + delta;
    if (target < 0 || target >= list.length) {
        return;
    }
    [list[index], list[target]] = [list[target], list[index]];
    settings.value.companies = list;
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2 md:col-span-2">
                <Label>Heading</Label>
                <Input v-model="data.heading" />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Subheading</Label>
                <Textarea v-model="data.subheading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>"All" filter label</Label>
                <Input v-model="data.all_label" />
            </div>
            <div class="grid gap-2">
                <Label>Reviews label</Label>
                <Input v-model="data.score_label" placeholder="Bewertungen" />
            </div>
            <div class="grid gap-2">
                <Label>Professional reviews label</Label>
                <Input
                    v-model="data.pro_reviews_label"
                    placeholder="Bewertungen als Profi"
                />
            </div>
            <div class="grid gap-2">
                <Label>Rating section prefix</Label>
                <Input
                    v-model="data.rating_prefix"
                    placeholder="So bewerten Kunden"
                />
            </div>
            <div class="grid gap-2">
                <Label>Pros heading</Label>
                <Input v-model="data.pros_heading" placeholder="Vorteile" />
            </div>
            <div class="grid gap-2">
                <Label>Cons heading</Label>
                <Input v-model="data.cons_heading" placeholder="Nachteile" />
            </div>
            <div class="grid gap-2">
                <Label>Button label</Label>
                <Input v-model="data.quote_label" placeholder="Angebot anfordern" />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Companies</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addCompany"
                >
                    <Plus class="size-4" />
                    Add company
                </Button>
            </div>

            <div
                v-for="(company, i) in settings.companies ?? []"
                :key="i"
                class="space-y-3 rounded-md border bg-muted/30 p-3"
            >
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-muted-foreground">
                        #{{ i + 1 }}
                    </span>
                    <div class="flex items-center gap-1">
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            :disabled="i === 0"
                            @click="move(i, -1)"
                        >
                            <ChevronUp class="size-4" />
                        </Button>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            :disabled="
                                i === (settings.companies?.length ?? 0) - 1
                            "
                            @click="move(i, 1)"
                        >
                            <ChevronDown class="size-4" />
                        </Button>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="removeCompany(i)"
                        >
                            <Trash2 class="size-4 text-destructive" />
                        </Button>
                    </div>
                </div>

                <div class="grid gap-3 md:grid-cols-[150px_1fr]">
                    <WidgetImageField
                        label="Logo"
                        aspect-class="aspect-[4/3] w-full"
                        :path="company.logo_path"
                        :url="company.logo_url"
                        @update="
                            (v) => {
                                company.logo_path = v.path;
                                company.logo_url = v.url;
                            }
                        "
                    />
                    <div class="space-y-2">
                        <Input
                            v-model="company.name"
                            placeholder="Company name"
                        />
                        <div class="grid gap-2 md:grid-cols-3">
                            <Input
                                v-model="company.score"
                                placeholder="Score (e.g. 9,8)"
                            />
                            <Input
                                v-model.number="company.reviews_count"
                                type="number"
                                min="0"
                                placeholder="Reviews count"
                            />
                            <Input
                                v-model.number="company.pro_reviews_count"
                                type="number"
                                min="0"
                                placeholder="Pro reviews count"
                            />
                        </div>
                        <Input
                            v-model="company.ribbon_label"
                            placeholder="Ribbon badge (e.g. Bestbewertetes Umzugsunternehmen)"
                        />
                    </div>
                </div>

                <RichTextEditor
                    v-model="company.description"
                    placeholder="Short description"
                />

                <Input v-model="company.address" placeholder="Location" />

                <Input
                    v-model="company.services"
                    placeholder="Services, comma separated (used as filter tags)"
                />

                <div class="grid gap-2 md:grid-cols-2">
                    <div class="grid gap-1">
                        <Label class="text-xs">Advantages</Label>
                        <Input
                            v-model="company.pros"
                            placeholder="Professionell, Freundlich, Fairer Preis"
                        />
                    </div>
                    <div class="grid gap-1">
                        <Label class="text-xs">Disadvantages</Label>
                        <Input
                            v-model="company.cons"
                            placeholder="Terminplanung"
                        />
                    </div>
                </div>
                <p class="text-xs text-muted-foreground">
                    Advantages and disadvantages are comma separated lists.
                </p>

                <Input
                    v-model="company.quote_url"
                    placeholder="Button link"
                />
            </div>
        </div>
    </div>
</template>
