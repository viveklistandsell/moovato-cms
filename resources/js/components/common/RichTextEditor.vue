<script setup lang="ts">
import { Extension } from '@tiptap/core';
import { CharacterCount } from '@tiptap/extension-character-count';
import { CodeBlockLowlight } from '@tiptap/extension-code-block-lowlight';
import { Color } from '@tiptap/extension-color';
import { FontFamily } from '@tiptap/extension-font-family';
import { Highlight } from '@tiptap/extension-highlight';
import { Image } from '@tiptap/extension-image';
import { Link } from '@tiptap/extension-link';
import { Placeholder } from '@tiptap/extension-placeholder';
import { Subscript } from '@tiptap/extension-subscript';
import { Superscript } from '@tiptap/extension-superscript';
import { Table } from '@tiptap/extension-table';
import { TableCell } from '@tiptap/extension-table-cell';
import { TableHeader } from '@tiptap/extension-table-header';
import { TableRow } from '@tiptap/extension-table-row';
import { TaskItem } from '@tiptap/extension-task-item';
import { TaskList } from '@tiptap/extension-task-list';
import { TextAlign } from '@tiptap/extension-text-align';
import { TextStyle } from '@tiptap/extension-text-style';
import { Typography } from '@tiptap/extension-typography';
import { Underline } from '@tiptap/extension-underline';
import { Youtube } from '@tiptap/extension-youtube';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { common, createLowlight } from 'lowlight';
import {
    AlignCenter,
    AlignLeft,
    AlignRight,
    Bold,
    ChevronDown,
    Code,
    FileCode,
    Heading1,
    Heading2,
    Heading3,
    Highlighter,
    Image as ImageIcon,
    Italic,
    Keyboard,
    Link as LinkIcon,
    List,
    ListChecks,
    ListOrdered,
    Maximize2,
    Minimize2,
    Palette,
    Quote,
    Redo,
    RemoveFormatting,
    Strikethrough,
    Subscript as SubscriptIcon,
    Superscript as SuperscriptIcon,
    Table as TableIcon,
    Type,
    Underline as UnderlineIcon,
    Undo,
    Video,
    X,
} from 'lucide-vue-next';
import { onClickOutside } from '@vueuse/core';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import MediaPicker from '@/components/common/MediaPicker.vue';

const lowlight = createLowlight(common);

// Custom FontSize extension — adds a `fontSize` attribute to the textStyle mark
// so we can do <span style="font-size: 14px">…</span> via setFontSize() / unsetFontSize().
const FontSize = Extension.create({
    name: 'fontSize',
    addOptions() {
        return { types: ['textStyle'] };
    },
    addGlobalAttributes() {
        return [
            {
                types: this.options.types,
                attributes: {
                    fontSize: {
                        default: null,
                        parseHTML: (el: HTMLElement) =>
                            el.style.fontSize?.replace(/['"]+/g, '') || null,
                        renderHTML: (attrs: { fontSize?: string | null }) =>
                            attrs.fontSize
                                ? { style: `font-size: ${attrs.fontSize}` }
                                : {},
                    },
                },
            },
        ];
    },
    addCommands() {
        return {
            setFontSize:
                (size: string) =>
                ({ chain }: { chain: () => any }) =>
                    chain()
                        .setMark('textStyle', { fontSize: size })
                        .run(),
            unsetFontSize:
                () =>
                ({ chain }: { chain: () => any }) =>
                    chain()
                        .setMark('textStyle', { fontSize: null })
                        .removeEmptyTextStyle()
                        .run(),
        } as Record<string, unknown>;
    },
});

const props = withDefaults(
    defineProps<{
        modelValue: string | null;
        placeholder?: string;
    }>(),
    { placeholder: 'Start writing…' },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'word-count', value: number): void;
}>();

const editor = useEditor({
    content: props.modelValue ?? '',
    extensions: [
        StarterKit.configure({ codeBlock: false }),
        Placeholder.configure({ placeholder: props.placeholder }),
        Typography,
        CharacterCount,
        Underline,
        Subscript,
        Superscript,
        Highlight.configure({ multicolor: true }),
        TaskList,
        TaskItem.configure({ nested: true }),
        Link.configure({
            openOnClick: false,
            HTMLAttributes: { class: 'text-primary underline' },
        }),
        Image.configure({ inline: false }),
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        TextStyle,
        Color,
        FontFamily,
        FontSize,
        Table.configure({ resizable: true }),
        TableRow,
        TableHeader,
        TableCell,
        Youtube.configure({ controls: true, nocookie: true, width: 640, height: 360 }),
        CodeBlockLowlight.configure({ lowlight }),
    ],
    editorProps: {
        attributes: {
            class:
                'prose prose-sm dark:prose-invert max-w-none min-h-[280px] px-4 py-3 focus:outline-none',
        },
    },
    onUpdate: ({ editor }) => {
        const html = editor.getHTML();
        emit('update:modelValue', html);
        emit('word-count', editor.storage.characterCount.words());
    },
});

watch(
    () => props.modelValue,
    (value) => {
        if (!editor.value) {
            return;
        }
        const current = editor.value.getHTML();
        if (value !== current) {
            editor.value.commands.setContent(value ?? '', { emitUpdate: false });
        }
    },
);

onBeforeUnmount(() => {
    editor.value?.destroy();
});

function isActive(
    name: string | Record<string, unknown>,
    attrs?: Record<string, unknown>,
): boolean {
    if (!editor.value) {
        return false;
    }
    if (typeof name === 'string') {
        return editor.value.isActive(name, attrs);
    }
    return editor.value.isActive(name);
}

function exec(callback: () => void): void {
    editor.value?.chain().focus();
    callback();
}

const TEXT_COLORS = [
    '#000000', '#404040', '#737373', '#a3a3a3',
    '#ef4444', '#f97316', '#eab308', '#22c55e',
    '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899',
];
const HIGHLIGHT_COLORS = [
    '#fef08a', '#fde68a', '#bbf7d0', '#a5f3fc',
    '#bfdbfe', '#ddd6fe', '#fbcfe8', '#fecaca',
];
const FONT_FAMILIES = [
    { label: 'Default', value: '' },
    { label: 'Sans-serif', value: 'ui-sans-serif, system-ui, sans-serif' },
    { label: 'Serif', value: 'ui-serif, Georgia, serif' },
    { label: 'Monospace', value: 'ui-monospace, SFMono-Regular, Menlo, monospace' },
    { label: 'Inter', value: 'Inter, sans-serif' },
    { label: 'Georgia', value: 'Georgia, serif' },
    { label: 'Courier', value: 'Courier New, monospace' },
];

const FONT_SIZES = [
    { label: 'Default', value: '' },
    { label: '12 px', value: '12px' },
    { label: '14 px', value: '14px' },
    { label: '16 px', value: '16px' },
    { label: '18 px', value: '18px' },
    { label: '20 px', value: '20px' },
    { label: '24 px', value: '24px' },
    { label: '32 px', value: '32px' },
    { label: '40 px', value: '40px' },
    { label: '48 px', value: '48px' },
];

const colorMenuRef = ref<HTMLDivElement | null>(null);
const highlightMenuRef = ref<HTMLDivElement | null>(null);
const tableMenuRef = ref<HTMLDivElement | null>(null);
const fontMenuRef = ref<HTMLDivElement | null>(null);
const sizeMenuRef = ref<HTMLDivElement | null>(null);

const colorMenuOpen = ref(false);
const highlightMenuOpen = ref(false);
const tableMenuOpen = ref(false);
const fontMenuOpen = ref(false);
const sizeMenuOpen = ref(false);
const helpOpen = ref(false);
const isFullscreen = ref(false);
const sourceMode = ref(false);
const sourceHtml = ref('');

onClickOutside(colorMenuRef, () => (colorMenuOpen.value = false));
onClickOutside(highlightMenuRef, () => (highlightMenuOpen.value = false));
onClickOutside(tableMenuRef, () => (tableMenuOpen.value = false));
onClickOutside(fontMenuRef, () => (fontMenuOpen.value = false));
onClickOutside(sizeMenuRef, () => (sizeMenuOpen.value = false));

function setColor(color: string) {
    exec(() => editor.value?.chain().focus().setColor(color).run());
    colorMenuOpen.value = false;
}
function unsetColor() {
    exec(() => editor.value?.chain().focus().unsetColor().run());
    colorMenuOpen.value = false;
}
function setHighlight(color: string) {
    exec(() => editor.value?.chain().focus().toggleHighlight({ color }).run());
    highlightMenuOpen.value = false;
}
function unsetHighlight() {
    exec(() => editor.value?.chain().focus().unsetHighlight().run());
    highlightMenuOpen.value = false;
}
function setFontFamily(family: string) {
    if (family === '') {
        exec(() => editor.value?.chain().focus().unsetFontFamily().run());
    } else {
        exec(() => editor.value?.chain().focus().setFontFamily(family).run());
    }
    fontMenuOpen.value = false;
}
function setFontSize(size: string) {
    if (size === '') {
        exec(() => (editor.value?.chain().focus() as any).unsetFontSize().run());
    } else {
        exec(() => (editor.value?.chain().focus() as any).setFontSize(size).run());
    }
    sizeMenuOpen.value = false;
}
function clearFormat() {
    exec(() =>
        editor.value?.chain().focus().clearNodes().unsetAllMarks().run(),
    );
}
function toggleTaskList() {
    exec(() => editor.value?.chain().focus().toggleTaskList().run());
}
function toggleSubscript() {
    exec(() => editor.value?.chain().focus().toggleSubscript().run());
}
function toggleSuperscript() {
    exec(() => editor.value?.chain().focus().toggleSuperscript().run());
}
function toggleCodeBlock() {
    exec(() => editor.value?.chain().focus().toggleCodeBlock().run());
}
function insertTable() {
    exec(() =>
        editor.value
            ?.chain()
            .focus()
            .insertTable({ rows: 3, cols: 3, withHeaderRow: true })
            .run(),
    );
    tableMenuOpen.value = false;
}
function addRowAfter() {
    exec(() => editor.value?.chain().focus().addRowAfter().run());
    tableMenuOpen.value = false;
}
function addColAfter() {
    exec(() => editor.value?.chain().focus().addColumnAfter().run());
    tableMenuOpen.value = false;
}
function deleteRow() {
    exec(() => editor.value?.chain().focus().deleteRow().run());
    tableMenuOpen.value = false;
}
function deleteCol() {
    exec(() => editor.value?.chain().focus().deleteColumn().run());
    tableMenuOpen.value = false;
}
function deleteTable() {
    exec(() => editor.value?.chain().focus().deleteTable().run());
    tableMenuOpen.value = false;
}
// MediaPicker integration: the image and video toolbar buttons open the
// in-form media library instead of the native window.prompt(). For images we
// insert via TipTap's setImage command; for video files we insert a raw
// <video controls> tag so the picked MP4/WebM file plays inline.
const imagePickerOpen = ref(false);
const videoPickerOpen = ref(false);

function onPickImage(file: { path: string; url: string; name: string }): void {
    if (!editor.value) return;
    editor.value.chain().focus().setImage({ src: file.url, alt: file.name }).run();
}

function onPickVideo(file: { path: string; url: string; name: string }): void {
    if (!editor.value) return;
    const html = `<video controls preload="metadata" style="max-width:100%;height:auto" src="${file.url}"></video>`;
    editor.value.chain().focus().insertContent(html).run();
}
function setLink() {
    if (!editor.value) {
        return;
    }
    const previous = editor.value.getAttributes('link').href as
        | string
        | undefined;
    const url = window.prompt('URL (leave empty to remove):', previous ?? '');
    if (url === null) {
        return;
    }
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }
    editor.value
        .chain()
        .focus()
        .extendMarkRange('link')
        .setLink({ href: url })
        .run();
}
function toggleFullscreen() {
    isFullscreen.value = !isFullscreen.value;
}
function toggleSourceMode() {
    if (!editor.value) {
        return;
    }
    if (!sourceMode.value) {
        sourceHtml.value = editor.value.getHTML();
        sourceMode.value = true;
    } else {
        editor.value.commands.setContent(sourceHtml.value);
        emit('update:modelValue', sourceHtml.value);
        sourceMode.value = false;
    }
}
function onSourceInput(event: Event) {
    sourceHtml.value = (event.target as HTMLTextAreaElement).value;
}

const tableActive = computed(() => isActive('table'));

const charCount = computed(() => editor.value?.storage.characterCount?.characters() ?? 0);
const wordCount = computed(() => editor.value?.storage.characterCount?.words() ?? 0);

function toggleBold() {
    exec(() => editor.value?.chain().focus().toggleBold().run());
}
function toggleItalic() {
    exec(() => editor.value?.chain().focus().toggleItalic().run());
}
function toggleUnderline() {
    exec(() => editor.value?.chain().focus().toggleUnderline().run());
}
function toggleStrike() {
    exec(() => editor.value?.chain().focus().toggleStrike().run());
}
function toggleCode() {
    exec(() => editor.value?.chain().focus().toggleCode().run());
}
function toggleH(level: 1 | 2 | 3) {
    exec(() => editor.value?.chain().focus().toggleHeading({ level }).run());
}
function toggleBullet() {
    exec(() => editor.value?.chain().focus().toggleBulletList().run());
}
function toggleOrdered() {
    exec(() => editor.value?.chain().focus().toggleOrderedList().run());
}
function toggleQuote() {
    exec(() => editor.value?.chain().focus().toggleBlockquote().run());
}
function setAlign(value: 'left' | 'center' | 'right') {
    exec(() => editor.value?.chain().focus().setTextAlign(value).run());
}
function undo() {
    exec(() => editor.value?.chain().focus().undo().run());
}
function redo() {
    exec(() => editor.value?.chain().focus().redo().run());
}
</script>

<template>
    <div
        class="overflow-hidden rounded-md border border-input bg-background dark:bg-input/30"
        :class="{
            'fixed inset-4 z-50 flex flex-col shadow-2xl': isFullscreen,
        }"
    >
        <div
            class="flex flex-wrap items-center gap-0.5 border-b border-input bg-muted/40 p-1"
        >
            <!-- Text marks -->
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('bold') }" title="Bold (Ctrl+B)" @click="toggleBold"><Bold class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('italic') }" title="Italic (Ctrl+I)" @click="toggleItalic"><Italic class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('underline') }" title="Underline (Ctrl+U)" @click="toggleUnderline"><UnderlineIcon class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('strike') }" title="Strikethrough" @click="toggleStrike"><Strikethrough class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('code') }" title="Inline code" @click="toggleCode"><Code class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('subscript') }" title="Subscript" @click="toggleSubscript"><SubscriptIcon class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('superscript') }" title="Superscript" @click="toggleSuperscript"><SuperscriptIcon class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" title="Clear formatting" @click="clearFormat"><RemoveFormatting class="size-4" /></button>

            <span class="mx-1 h-5 w-px bg-border" />

            <!-- Font family -->
            <div ref="fontMenuRef" class="relative">
                <button type="button" class="flex items-center gap-0.5 rounded p-1.5 hover:bg-muted" title="Font family" @click="fontMenuOpen = !fontMenuOpen"><Type class="size-4" /><ChevronDown class="size-3 opacity-60" /></button>
                <div v-if="fontMenuOpen" class="absolute left-0 top-full z-10 mt-1 w-48 rounded-md border bg-popover py-1 text-sm shadow-md">
                    <button v-for="f in FONT_FAMILIES" :key="f.label" type="button" class="block w-full px-3 py-1.5 text-left hover:bg-muted" :style="f.value ? { fontFamily: f.value } : {}" @click="setFontFamily(f.value)">{{ f.label }}</button>
                </div>
            </div>

            <!-- Font size -->
            <div ref="sizeMenuRef" class="relative">
                <button type="button" class="flex items-center gap-0.5 rounded p-1.5 hover:bg-muted" title="Font size" @click="sizeMenuOpen = !sizeMenuOpen"><span class="px-1 text-xs font-semibold">A↕</span><ChevronDown class="size-3 opacity-60" /></button>
                <div v-if="sizeMenuOpen" class="absolute left-0 top-full z-10 mt-1 w-32 rounded-md border bg-popover py-1 text-sm shadow-md">
                    <button v-for="s in FONT_SIZES" :key="s.label" type="button" class="block w-full px-3 py-1.5 text-left hover:bg-muted" :style="s.value ? { fontSize: s.value } : {}" @click="setFontSize(s.value)">{{ s.label }}</button>
                </div>
            </div>

            <!-- Color -->
            <div ref="colorMenuRef" class="relative">
                <button type="button" class="flex items-center gap-0.5 rounded p-1.5 hover:bg-muted" title="Text color" @click="colorMenuOpen = !colorMenuOpen"><Palette class="size-4" /><ChevronDown class="size-3 opacity-60" /></button>
                <div v-if="colorMenuOpen" class="absolute left-0 top-full z-10 mt-1 w-44 rounded-md border bg-popover p-2 shadow-md">
                    <div class="grid grid-cols-6 gap-1">
                        <button v-for="c in TEXT_COLORS" :key="c" type="button" class="size-6 rounded border transition-transform hover:scale-110" :style="{ backgroundColor: c }" :title="c" @click="setColor(c)" />
                    </div>
                    <button type="button" class="mt-2 w-full rounded px-2 py-1 text-xs text-muted-foreground hover:bg-muted" @click="unsetColor">Reset color</button>
                </div>
            </div>

            <!-- Highlight -->
            <div ref="highlightMenuRef" class="relative">
                <button type="button" class="flex items-center gap-0.5 rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('highlight') }" title="Highlight" @click="highlightMenuOpen = !highlightMenuOpen"><Highlighter class="size-4" /><ChevronDown class="size-3 opacity-60" /></button>
                <div v-if="highlightMenuOpen" class="absolute left-0 top-full z-10 mt-1 w-44 rounded-md border bg-popover p-2 shadow-md">
                    <div class="grid grid-cols-4 gap-1">
                        <button v-for="c in HIGHLIGHT_COLORS" :key="c" type="button" class="size-7 rounded border transition-transform hover:scale-110" :style="{ backgroundColor: c }" :title="c" @click="setHighlight(c)" />
                    </div>
                    <button type="button" class="mt-2 w-full rounded px-2 py-1 text-xs text-muted-foreground hover:bg-muted" @click="unsetHighlight">Remove highlight</button>
                </div>
            </div>

            <span class="mx-1 h-5 w-px bg-border" />

            <!-- Headings -->
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('heading', { level: 1 }) }" title="Heading 1" @click="toggleH(1)"><Heading1 class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('heading', { level: 2 }) }" title="Heading 2" @click="toggleH(2)"><Heading2 class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('heading', { level: 3 }) }" title="Heading 3" @click="toggleH(3)"><Heading3 class="size-4" /></button>

            <span class="mx-1 h-5 w-px bg-border" />

            <!-- Lists -->
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('bulletList') }" title="Bullet list" @click="toggleBullet"><List class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('orderedList') }" title="Ordered list" @click="toggleOrdered"><ListOrdered class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('taskList') }" title="Task list (checkboxes)" @click="toggleTaskList"><ListChecks class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('blockquote') }" title="Quote" @click="toggleQuote"><Quote class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('codeBlock') }" title="Code block (with syntax highlighting)" @click="toggleCodeBlock"><FileCode class="size-4" /></button>

            <span class="mx-1 h-5 w-px bg-border" />

            <!-- Alignment -->
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive({ textAlign: 'left' }) }" title="Align left" @click="setAlign('left')"><AlignLeft class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive({ textAlign: 'center' }) }" title="Align center" @click="setAlign('center')"><AlignCenter class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive({ textAlign: 'right' }) }" title="Align right" @click="setAlign('right')"><AlignRight class="size-4" /></button>

            <span class="mx-1 h-5 w-px bg-border" />

            <!-- Insert -->
            <div ref="tableMenuRef" class="relative">
                <button type="button" class="flex items-center gap-0.5 rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': tableActive }" title="Table" @click="tableMenuOpen = !tableMenuOpen"><TableIcon class="size-4" /><ChevronDown class="size-3 opacity-60" /></button>
                <div v-if="tableMenuOpen" class="absolute left-0 top-full z-10 mt-1 w-48 rounded-md border bg-popover py-1 text-sm shadow-md">
                    <button type="button" class="block w-full px-3 py-1.5 text-left hover:bg-muted" @click="insertTable">Insert 3×3 table</button>
                    <div class="my-1 border-t" />
                    <button type="button" class="block w-full px-3 py-1.5 text-left hover:bg-muted disabled:opacity-50" :disabled="!tableActive" @click="addRowAfter">Add row below</button>
                    <button type="button" class="block w-full px-3 py-1.5 text-left hover:bg-muted disabled:opacity-50" :disabled="!tableActive" @click="addColAfter">Add column right</button>
                    <div class="my-1 border-t" />
                    <button type="button" class="block w-full px-3 py-1.5 text-left text-destructive hover:bg-muted disabled:opacity-50" :disabled="!tableActive" @click="deleteRow">Delete row</button>
                    <button type="button" class="block w-full px-3 py-1.5 text-left text-destructive hover:bg-muted disabled:opacity-50" :disabled="!tableActive" @click="deleteCol">Delete column</button>
                    <button type="button" class="block w-full px-3 py-1.5 text-left text-destructive hover:bg-muted disabled:opacity-50" :disabled="!tableActive" @click="deleteTable">Delete table</button>
                </div>
            </div>

            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': isActive('link') }" title="Link (Ctrl+K)" @click="setLink"><LinkIcon class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" title="Insert image from media library" @click="imagePickerOpen = true"><ImageIcon class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" title="Insert video from media library" @click="videoPickerOpen = true"><Video class="size-4" /></button>

            <span class="mx-1 h-5 w-px bg-border" />

            <!-- Source / fullscreen -->
            <button type="button" class="rounded p-1.5 hover:bg-muted" :class="{ 'bg-muted text-foreground': sourceMode }" title="View HTML source" @click="toggleSourceMode"><FileCode class="size-4" /></button>
            <button type="button" class="rounded p-1.5 hover:bg-muted" :title="isFullscreen ? 'Exit fullscreen' : 'Fullscreen'" @click="toggleFullscreen"><component :is="isFullscreen ? Minimize2 : Maximize2" class="size-4" /></button>

            <span class="ml-auto flex items-center gap-0.5">
                <button type="button" class="rounded p-1.5 hover:bg-muted disabled:opacity-40" title="Undo (Ctrl+Z)" :disabled="!editor?.can().undo()" @click="undo"><Undo class="size-4" /></button>
                <button type="button" class="rounded p-1.5 hover:bg-muted disabled:opacity-40" title="Redo (Ctrl+Shift+Z)" :disabled="!editor?.can().redo()" @click="redo"><Redo class="size-4" /></button>
                <button type="button" class="rounded p-1.5 hover:bg-muted" title="Keyboard shortcuts" @click="helpOpen = true"><Keyboard class="size-4" /></button>
            </span>
        </div>

        <!-- Body -->
        <div class="bg-background dark:bg-input/30" :class="{ 'flex-1 overflow-y-auto': isFullscreen }">
            <textarea
                v-if="sourceMode"
                :value="sourceHtml"
                class="block min-h-[280px] w-full resize-y bg-transparent p-4 font-mono text-xs outline-none"
                :class="{ 'h-full': isFullscreen }"
                spellcheck="false"
                @input="onSourceInput"
            />
            <EditorContent v-else :editor="editor" />
        </div>

        <!-- Footer status bar -->
        <div class="flex items-center justify-between border-t border-input bg-muted/30 px-3 py-1.5 text-[11px] text-muted-foreground">
            <span>{{ wordCount }} words · {{ charCount }} characters</span>
            <span v-if="isFullscreen">Press fullscreen icon to exit</span>
        </div>

        <!-- Help -->
        <div v-if="helpOpen" class="absolute inset-0 z-20 flex items-center justify-center bg-black/50 p-4" @click.self="helpOpen = false">
            <div class="w-full max-w-md rounded-md border bg-background p-5 shadow-xl">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-base font-semibold">Keyboard shortcuts</h3>
                    <button type="button" class="rounded p-1 hover:bg-muted" @click="helpOpen = false"><X class="size-4" /></button>
                </div>
                <dl class="space-y-1.5 text-sm">
                    <div class="flex justify-between"><dt>Bold</dt><dd class="font-mono text-xs">Ctrl/⌘ + B</dd></div>
                    <div class="flex justify-between"><dt>Italic</dt><dd class="font-mono text-xs">Ctrl/⌘ + I</dd></div>
                    <div class="flex justify-between"><dt>Underline</dt><dd class="font-mono text-xs">Ctrl/⌘ + U</dd></div>
                    <div class="flex justify-between"><dt>Strikethrough</dt><dd class="font-mono text-xs">Ctrl/⌘ + Shift + X</dd></div>
                    <div class="flex justify-between"><dt>Heading 1 / 2 / 3</dt><dd class="font-mono text-xs">Ctrl/⌘ + Alt + 1 / 2 / 3</dd></div>
                    <div class="flex justify-between"><dt>Bullet list</dt><dd class="font-mono text-xs">Ctrl/⌘ + Shift + 8</dd></div>
                    <div class="flex justify-between"><dt>Ordered list</dt><dd class="font-mono text-xs">Ctrl/⌘ + Shift + 7</dd></div>
                    <div class="flex justify-between"><dt>Task list</dt><dd class="font-mono text-xs">Ctrl/⌘ + Shift + 9</dd></div>
                    <div class="flex justify-between"><dt>Blockquote</dt><dd class="font-mono text-xs">Ctrl/⌘ + Shift + B</dd></div>
                    <div class="flex justify-between"><dt>Code block</dt><dd class="font-mono text-xs">Ctrl/⌘ + Alt + C</dd></div>
                    <div class="flex justify-between"><dt>Link</dt><dd class="font-mono text-xs">Ctrl/⌘ + K</dd></div>
                    <div class="flex justify-between"><dt>Undo / Redo</dt><dd class="font-mono text-xs">Ctrl/⌘ + Z / Shift + Z</dd></div>
                </dl>
                <p class="mt-3 text-xs text-muted-foreground">Typography auto-formats: -- → —, ... → …, "x" → "x"</p>
            </div>
        </div>

        <!-- Media library pickers driven by the image and video toolbar buttons. -->
        <MediaPicker v-model:open="imagePickerOpen" accept="image" @pick="onPickImage" />
        <MediaPicker v-model:open="videoPickerOpen" accept="video" @pick="onPickVideo" />
    </div>
</template>

<style>
.tiptap.ProseMirror p.is-editor-empty:first-child::before {
    color: var(--muted-foreground);
    content: attr(data-placeholder);
    float: left;
    height: 0;
    pointer-events: none;
}

/* Tables */
.tiptap table {
    border-collapse: collapse;
    margin: 0;
    overflow: hidden;
    table-layout: fixed;
    width: 100%;
}
.tiptap td,
.tiptap th {
    border: 1px solid var(--border);
    box-sizing: border-box;
    min-width: 1em;
    padding: 6px 8px;
    position: relative;
    vertical-align: top;
}
.tiptap th {
    background-color: var(--muted);
    font-weight: 600;
    text-align: left;
}

/* Highlight mark */
.tiptap mark {
    border-radius: 0.125rem;
    padding: 0 0.125rem;
}

/* Task list */
.tiptap ul[data-type='taskList'] {
    list-style: none;
    padding: 0;
}
.tiptap ul[data-type='taskList'] li {
    align-items: flex-start;
    display: flex;
    gap: 0.5rem;
}
.tiptap ul[data-type='taskList'] li > label {
    flex: 0 0 auto;
    margin-top: 0.25rem;
    user-select: none;
}
.tiptap ul[data-type='taskList'] li > div {
    flex: 1 1 auto;
}
.tiptap ul[data-type='taskList'] input[type='checkbox'] {
    cursor: pointer;
}

/* Code block (lowlight syntax highlighting) */
.tiptap pre {
    background: #0d1117;
    color: #e6edf3;
    border-radius: 0.375rem;
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    padding: 0.75rem 1rem;
    overflow-x: auto;
}
.tiptap pre code {
    background: none;
    color: inherit;
    font-size: 0.85rem;
    padding: 0;
}
.tiptap .hljs-comment,
.tiptap .hljs-quote {
    color: #8b949e;
    font-style: italic;
}
.tiptap .hljs-keyword,
.tiptap .hljs-selector-tag,
.tiptap .hljs-meta {
    color: #ff7b72;
}
.tiptap .hljs-string,
.tiptap .hljs-attr {
    color: #a5d6ff;
}
.tiptap .hljs-number,
.tiptap .hljs-literal {
    color: #79c0ff;
}
.tiptap .hljs-function,
.tiptap .hljs-title {
    color: #d2a8ff;
}
.tiptap .hljs-built_in,
.tiptap .hljs-type {
    color: #ffa657;
}
</style>
