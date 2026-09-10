<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Partner = {
    path: string | null;
    url: string | null;
    name: string;
    link: string;
};

type Certificate = { icon: string; title: string };

type Settings = {
    partners: Partner[];
    certificates: Certificate[];
};

type Data = {
    eyebrow: string;
    heading: string;
    subheading: string;
    description: string;
    button_label: string;
    button_url: string;
    partners_title: string;
    certificates_title: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addPartner(): void {
    settings.value.partners = [
        ...(settings.value.partners ?? []),
        { path: null, url: null, name: '', link: '' },
    ];
}

function removePartner(index: number): void {
    settings.value.partners = (settings.value.partners ?? []).filter(
        (_, i) => i !== index,
    );
}

function addCertificate(): void {
    settings.value.certificates = [
        ...(settings.value.certificates ?? []),
        { icon: 'Award', title: '' },
    ];
}

function removeCertificate(index: number): void {
    settings.value.certificates = (settings.value.certificates ?? []).filter(
        (_, i) => i !== index,
    );
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input
                    v-model="data.eyebrow"
                    placeholder="Vertrauen & Qualität"
                />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Unsere Partner & Zertifikate"
                />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Subheading</Label>
                <Textarea v-model="data.subheading" :rows="2" />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Description</Label>
                <RichTextEditor
                    v-model="data.description"
                    placeholder="Beschreibung"
                />
            </div>
            <div class="grid gap-1">
                <Label class="text-xs">Button label (optional)</Label>
                <Input
                    v-model="data.button_label"
                    placeholder="Mehr erfahren"
                />
            </div>
            <div class="grid gap-1">
                <Label class="text-xs">Button URL</Label>
                <Input v-model="data.button_url" placeholder="#leistungen" />
            </div>
            <div class="grid gap-2">
                <Label>Partners title</Label>
                <Input
                    v-model="data.partners_title"
                    placeholder="Starke Partner an unserer Seite"
                />
            </div>
            <div class="grid gap-2">
                <Label>Certificates title</Label>
                <Input
                    v-model="data.certificates_title"
                    placeholder="Geprüft & zertifiziert"
                />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Partner logos</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addPartner"
                >
                    <Plus class="size-4" />
                    Add partner
                </Button>
            </div>
            <div
                v-for="(partner, i) in settings.partners ?? []"
                :key="i"
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[160px_1fr_auto]"
            >
                <WidgetImageField
                    aspect-class="aspect-[3/2] w-full"
                    :path="partner.path"
                    :url="partner.url"
                    @update="
                        (v) => {
                            partner.path = v.path;
                            partner.url = v.url;
                        }
                    "
                />
                <div class="space-y-2">
                    <Input v-model="partner.name" placeholder="Partner name" />
                    <Input
                        v-model="partner.link"
                        placeholder="https:// (optional)"
                    />
                </div>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removePartner(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Certificates</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addCertificate"
                >
                    <Plus class="size-4" />
                    Add certificate
                </Button>
            </div>
            <div
                v-for="(cert, i) in settings.certificates ?? []"
                :key="i"
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[160px_1fr_auto]"
            >
                <Input v-model="cert.icon" placeholder="Icon (lucide)" />
                <Input v-model="cert.title" placeholder="DEKRA geprüft" />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeCertificate(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
