<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Service = { icon: string; label: string };

type Settings = {
    dashboard_image_path: string | null;
    dashboard_image_url: string | null;
    reviews_image_path: string | null;
    reviews_image_url: string | null;
};

type Data = {
    promo_title: string;
    promo_description: string;
    promo_text: string;
    promo_cta_label: string;
    promo_cta_url: string;
    dashboard_image_alt: string;
    reviews_title: string;
    reviews_text: string;
    reviews_image_alt: string;
    services_title: string;
    services_text: string;
    services_link_label: string;
    services_link_url: string;
    services: Service[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addService(): void {
    data.value.services = [
        ...(data.value.services ?? []),
        { icon: 'Box', label: '' },
    ];
}

function removeService(index: number): void {
    data.value.services = (data.value.services ?? []).filter(
        (_, i) => i !== index,
    );
}
</script>

<template>
    <div class="space-y-6">
        <!-- Promo card -->
        <div class="space-y-3 rounded-md border bg-muted/30 p-3">
            <Label class="text-sm font-semibold">Promo card</Label>
            <div class="grid gap-3 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Title</Label>
                    <Input
                        v-model="data.promo_title"
                        placeholder="Ihr persönliches Dashboard"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>Text</Label>
                    <Textarea v-model="data.promo_text" :rows="2" />
                </div>
                <div class="grid gap-2 md:col-span-2">
                    <Label>Description</Label>
                    <RichTextEditor
                        v-model="data.promo_description"
                        placeholder="Beschreibung"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>CTA label</Label>
                    <Input
                        v-model="data.promo_cta_label"
                        placeholder="Jetzt kostenlos loslegen"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>CTA URL</Label>
                    <Input v-model="data.promo_cta_url" placeholder="#" />
                </div>
            </div>
            <WidgetImageField
                label="Dashboard image"
                :path="settings.dashboard_image_path"
                :url="settings.dashboard_image_url"
                @update="
                    (v) => {
                        settings.dashboard_image_path = v.path;
                        settings.dashboard_image_url = v.url;
                    }
                "
            />
            <div class="grid gap-2">
                <Label>Dashboard image alt</Label>
                <Input
                    v-model="data.dashboard_image_alt"
                    placeholder="Moovato Dashboard"
                />
            </div>
        </div>

        <!-- Reviews card -->
        <div class="space-y-3 rounded-md border bg-muted/30 p-3">
            <Label class="text-sm font-semibold">Reviews card</Label>
            <div class="grid gap-2">
                <Label>Title</Label>
                <Input
                    v-model="data.reviews_title"
                    placeholder="Authentische Bewertungen"
                />
            </div>
            <div class="grid gap-2">
                <Label>Text</Label>
                <Textarea v-model="data.reviews_text" :rows="3" />
            </div>
            <WidgetImageField
                label="Reviews image"
                :path="settings.reviews_image_path"
                :url="settings.reviews_image_url"
                @update="
                    (v) => {
                        settings.reviews_image_path = v.path;
                        settings.reviews_image_url = v.url;
                    }
                "
            />
            <div class="grid gap-2">
                <Label>Reviews image alt</Label>
                <Input
                    v-model="data.reviews_image_alt"
                    placeholder="Kundenbewertungen"
                />
            </div>
        </div>

        <!-- Services card -->
        <div class="space-y-3 rounded-md border bg-muted/30 p-3">
            <Label class="text-sm font-semibold">Services card</Label>
            <div class="grid gap-2">
                <Label>Title</Label>
                <Input
                    v-model="data.services_title"
                    placeholder="Umzugsservices"
                />
            </div>
            <div class="grid gap-2">
                <Label>Text</Label>
                <Textarea v-model="data.services_text" :rows="3" />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Link label</Label>
                    <Input
                        v-model="data.services_link_label"
                        placeholder="Alle anzeigen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Link URL</Label>
                    <Input v-model="data.services_link_url" placeholder="#" />
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label class="text-xs font-semibold">Service items</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addService"
                    >
                        <Plus class="size-4" />
                        Add
                    </Button>
                </div>
                <div
                    v-for="(service, i) in data.services ?? []"
                    :key="i"
                    class="grid grid-cols-[120px_1fr_auto] gap-2"
                >
                    <Input v-model="service.icon" placeholder="Icon (lucide)" />
                    <Input
                        v-model="service.label"
                        placeholder="Umzugsversicherung"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removeService(i)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
