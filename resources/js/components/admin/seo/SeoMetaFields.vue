<script setup lang="ts">
/**
 * Per-locale SEO metadata block — reused by the Blog Post editor and
 * the Page editor. Four inputs:
 *
 *   - Meta Title        (single line, 60 char soft-cap counter)
 *   - Meta Description  (textarea, 160 char soft-cap counter)
 *   - Schema (JSON-LD)  (textarea, JSON.parse hint if malformed)
 *   - Meta Image        (path string, opens MediaPicker on pick)
 *
 * The parent binds each field via v-model:<name> — mirrors the way
 * the existing MediaPicker is wired on both editors. No knowledge of
 * the surrounding form's other translation fields (name / permalink
 * etc.) — this component only owns the SEO ones.
 */
import { Image as ImageIcon, Trash2, Upload } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import MediaPicker from '@/components/common/MediaPicker.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    metaTitle: string;
    metaDescription: string;
    schema: string;
    metaImage: string;
    /** Absolute or relative URL for the preview thumbnail. */
    metaImageUrl?: string | null;
    errors?: {
        meta_title?: string;
        meta_description?: string;
        schema?: string;
        meta_image?: string;
    };
    locale: string;
}>();

const emit = defineEmits<{
    (e: 'update:metaTitle', value: string): void;
    (e: 'update:metaDescription', value: string): void;
    (e: 'update:schema', value: string): void;
    (e: 'update:metaImage', value: string): void;
}>();

const de = {
    section_title: 'SEO Meta-Tags',
    label_meta_title: 'Meta Title',
    label_meta_description: 'Meta Description',
    label_schema: 'Schema (JSON-LD)',
    label_meta_image: 'Meta Image',
    hint_title: 'Empfohlen: bis zu 60 Zeichen. Überschreibt den auto-generierten <title>.',
    hint_description: 'Empfohlen: bis zu 160 Zeichen. Erscheint als <meta name="description">.',
    hint_schema: 'Nur gültiges JSON-Objekt einfügen — der script-Tag wird automatisch hinzugefügt.',
    hint_meta_image: 'Bild aus der Mediathek wählen. Erscheint als og:image / Twitter-Card-Bild.',
    invalid_json: 'Ungültiges JSON — die Änderung wird beim Speichern ignoriert.',
    pick_image: 'Bild auswählen',
    change_image: 'Bild ändern',
    remove_image: 'Entfernen',
    chars: 'Zeichen',
} as const;
const en = {
    section_title: 'SEO Meta Tags',
    label_meta_title: 'Meta Title',
    label_meta_description: 'Meta Description',
    label_schema: 'Schema (JSON-LD)',
    label_meta_image: 'Meta Image',
    hint_title: 'Recommended: up to 60 characters. Overrides the auto-generated <title>.',
    hint_description: 'Recommended: up to 160 characters. Renders as <meta name="description">.',
    hint_schema: 'Paste only a valid JSON object — the <script> tag is added automatically.',
    hint_meta_image: 'Pick from the media library. Renders as og:image / Twitter card image.',
    invalid_json: 'Invalid JSON — the change will be dropped on save.',
    pick_image: 'Choose image',
    change_image: 'Change image',
    remove_image: 'Remove',
    chars: 'chars',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const titleCount = computed(() => props.metaTitle.length);
const descriptionCount = computed(() => props.metaDescription.length);

// Cheap JSON.parse check so the admin sees a hint if their schema
// blob is malformed. Server also normalizes on save — this is just a
// UX affordance.
const schemaIsValid = computed<boolean>(() => {
    const raw = (props.schema ?? '').trim();
    if (raw === '') return true;
    try {
        const parsed = JSON.parse(raw);
        return typeof parsed === 'object' && parsed !== null;
    } catch {
        return false;
    }
});

/* -------------------- MediaPicker -------------------- */

const pickerOpen = ref(false);
const localPreview = ref<string | null>(props.metaImageUrl ?? null);

function openPicker(): void {
    pickerOpen.value = true;
}

function onMediaPicked(file: { path: string; url: string; name: string }): void {
    emit('update:metaImage', file.path);
    localPreview.value = file.url;
    pickerOpen.value = false;
}

function clearImage(): void {
    emit('update:metaImage', '');
    localPreview.value = null;
}

const previewSrc = computed<string | null>(() => {
    if (localPreview.value) return localPreview.value;
    if (props.metaImageUrl) return props.metaImageUrl;
    if (props.metaImage) {
        return props.metaImage.startsWith('http')
            ? props.metaImage
            : `/storage/${props.metaImage.replace(/^\//, '')}`;
    }
    return null;
});
</script>

<template>
    <section class="space-y-4 rounded-lg border border-border bg-card p-4">
        <header>
            <h3 class="text-sm font-semibold">{{ t.section_title }}</h3>
        </header>

        <div class="grid gap-4">
            <!-- Meta Title -->
            <div>
                <Label>{{ t.label_meta_title }}</Label>
                <Input
                    :model-value="props.metaTitle"
                    :placeholder="t.label_meta_title"
                    maxlength="255"
                    @update:model-value="(v) => emit('update:metaTitle', String(v ?? ''))"
                />
                <p class="mt-1 flex items-center justify-between text-[11px] text-muted-foreground">
                    <span>{{ t.hint_title }}</span>
                    <span :class="titleCount > 60 ? 'text-amber-600' : ''">{{ titleCount }} / 60 {{ t.chars }}</span>
                </p>
                <p v-if="props.errors?.meta_title" class="mt-1 text-xs text-destructive">
                    {{ props.errors.meta_title }}
                </p>
            </div>

            <!-- Meta Description -->
            <div>
                <Label>{{ t.label_meta_description }}</Label>
                <textarea
                    :value="props.metaDescription"
                    :placeholder="t.label_meta_description"
                    rows="3"
                    maxlength="1000"
                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    @input="(e) => emit('update:metaDescription', (e.target as HTMLTextAreaElement).value)"
                ></textarea>
                <p class="mt-1 flex items-center justify-between text-[11px] text-muted-foreground">
                    <span>{{ t.hint_description }}</span>
                    <span :class="descriptionCount > 160 ? 'text-amber-600' : ''">{{ descriptionCount }} / 160 {{ t.chars }}</span>
                </p>
                <p v-if="props.errors?.meta_description" class="mt-1 text-xs text-destructive">
                    {{ props.errors.meta_description }}
                </p>
            </div>

            <!-- Schema (JSON-LD) -->
            <div>
                <Label>{{ t.label_schema }}</Label>
                <textarea
                    :value="props.schema"
                    placeholder='{"@context":"https://schema.org","@type":"..."}'
                    rows="6"
                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 font-mono text-xs shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    :class="!schemaIsValid ? 'border-destructive' : ''"
                    @input="(e) => emit('update:schema', (e.target as HTMLTextAreaElement).value)"
                ></textarea>
                <p class="mt-1 text-[11px] text-muted-foreground">{{ t.hint_schema }}</p>
                <p v-if="!schemaIsValid" class="mt-1 text-xs text-destructive">{{ t.invalid_json }}</p>
                <p v-if="props.errors?.schema" class="mt-1 text-xs text-destructive">
                    {{ props.errors.schema }}
                </p>
            </div>

            <!-- Meta Image (MediaPicker) -->
            <div>
                <Label>{{ t.label_meta_image }}</Label>
                <div class="mt-1 flex items-start gap-3">
                    <div
                        v-if="previewSrc"
                        class="size-20 shrink-0 overflow-hidden rounded-md border bg-muted"
                    >
                        <img :src="previewSrc" alt="" class="size-full object-cover" />
                    </div>
                    <div
                        v-else
                        class="flex size-20 shrink-0 items-center justify-center rounded-md border border-dashed bg-muted text-muted-foreground"
                    >
                        <ImageIcon class="size-6" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Button type="button" variant="outline" size="sm" @click="openPicker">
                            <Upload class="size-3.5" />
                            {{ previewSrc ? t.change_image : t.pick_image }}
                        </Button>
                        <Button
                            v-if="previewSrc"
                            type="button"
                            variant="ghost"
                            size="sm"
                            class="text-destructive hover:text-destructive"
                            @click="clearImage"
                        >
                            <Trash2 class="size-3.5" />
                            {{ t.remove_image }}
                        </Button>
                    </div>
                </div>
                <p class="mt-1 text-[11px] text-muted-foreground">{{ t.hint_meta_image }}</p>
                <p v-if="props.errors?.meta_image" class="mt-1 text-xs text-destructive">
                    {{ props.errors.meta_image }}
                </p>
            </div>
        </div>

        <MediaPicker v-model:open="pickerOpen" @pick="onMediaPicked" />
    </section>
</template>
