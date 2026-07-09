<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';

type Item = { question: string; answer: string };
type Category = { name: string; items: Item[] };

type Settings = {
    show_search: boolean;
    show_categories: boolean;
    first_open: boolean;
};

type Data = {
    eyebrow: string;
    heading: string;
    subheading: string;
    search_placeholder: string;
    categories: Category[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addCategory(): void {
    data.value.categories = [
        ...(data.value.categories ?? []),
        { name: '', items: [{ question: '', answer: '' }] },
    ];
}

function removeCategory(index: number): void {
    data.value.categories = (data.value.categories ?? []).filter(
        (_, i) => i !== index,
    );
}

function addItem(category: Category): void {
    category.items = [...(category.items ?? []), { question: '', answer: '' }];
}

function removeItem(category: Category, index: number): void {
    category.items = (category.items ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Hilfe & Support" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Häufig gestellte Fragen"
                />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Subheading</Label>
                <Textarea v-model="data.subheading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>Search placeholder</Label>
                <Input
                    v-model="data.search_placeholder"
                    placeholder="Frage durchsuchen …"
                />
            </div>
        </div>

        <div
            class="grid gap-4 rounded-md border bg-muted/30 p-3 sm:grid-cols-3"
        >
            <div class="flex items-center justify-between gap-2">
                <Label for="faqpage-search">Show search</Label>
                <Switch
                    id="faqpage-search"
                    :model-value="settings.show_search"
                    @update:model-value="(v) => (settings.show_search = v)"
                />
            </div>
            <div class="flex items-center justify-between gap-2">
                <Label for="faqpage-cats">Show category filter</Label>
                <Switch
                    id="faqpage-cats"
                    :model-value="settings.show_categories"
                    @update:model-value="(v) => (settings.show_categories = v)"
                />
            </div>
            <div class="flex items-center justify-between gap-2">
                <Label for="faqpage-first">Open first item</Label>
                <Switch
                    id="faqpage-first"
                    :model-value="settings.first_open"
                    @update:model-value="(v) => (settings.first_open = v)"
                />
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Categories</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addCategory"
                >
                    <Plus class="size-4" />
                    Add category
                </Button>
            </div>

            <div
                v-for="(category, ci) in data.categories ?? []"
                :key="ci"
                class="space-y-3 rounded-lg border bg-muted/20 p-3"
            >
                <div class="flex items-center gap-2">
                    <Input
                        v-model="category.name"
                        placeholder="Category name"
                        class="flex-1 font-medium"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removeCategory(ci)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>

                <div
                    v-for="(item, ii) in category.items ?? []"
                    :key="ii"
                    class="grid gap-2 rounded-md border bg-card p-3"
                >
                    <div class="flex items-center gap-2">
                        <Input
                            v-model="item.question"
                            placeholder="Question"
                            class="flex-1"
                        />
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="removeItem(category, ii)"
                        >
                            <Trash2 class="size-4 text-destructive" />
                        </Button>
                    </div>
                    <RichTextEditor
                        v-model="item.answer"
                        placeholder="Answer"
                    />
                </div>

                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addItem(category)"
                >
                    <Plus class="size-4" />
                    Add question
                </Button>
            </div>
        </div>
    </div>
</template>
