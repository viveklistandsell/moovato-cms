<script setup lang="ts">
import {
    ClipboardCopy,
    Copy,
    Eye,
    EyeOff,
    GripVertical,
    Monitor,
    Pencil,
    Smartphone,
    Tablet,
    Trash2,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { useT } from '@/composables/useT';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';
import type { WidgetInstance, WidgetMeta } from '@/widgets/types';

const t = useT();

const props = defineProps<{
    widget: WidgetInstance;
    meta: WidgetMeta | null;
    defaultLang: string;
}>();

const emit = defineEmits<{
    (e: 'edit'): void;
    (e: 'duplicate'): void;
    (e: 'copy-to-clipboard'): void;
    (e: 'remove'): void;
    (e: 'toggle-active'): void;
}>();

// Pull whichever translatable label we can find to show as a snippet preview
// on the card. We try common keys (title, heading, message) in priority order.
const previewText = computed<string>(() => {
    const tr = props.widget.translations?.[props.defaultLang] ?? {};
    const candidates = ['title', 'heading', 'message', 'eyebrow'];
    for (const key of candidates) {
        const v = (tr as Record<string, unknown>)[key];
        if (typeof v === 'string' && v.length > 0) {
            return v;
        }
    }
    return '';
});

// Per-breakpoint visibility badge — only rendered when the widget is hidden
// on at least one breakpoint, so the card stays uncluttered for "show on all".
const visibilityHidden = computed(() => {
    const v = props.widget.visibility;
    if (!v) return null;
    if (v.desktop && v.tablet && v.mobile) return null;
    return {
        mobile: !v.mobile,
        tablet: !v.tablet,
        desktop: !v.desktop,
    };
});
</script>

<template>
    <div
        class="group flex items-stretch gap-2 rounded-lg border bg-card transition hover:border-primary/50 hover:shadow-sm"
        :class="{ 'opacity-60': !widget.is_active }"
    >
        <div
            class="widget-drag-handle flex cursor-grab items-center px-2 text-muted-foreground hover:text-foreground"
            title="Drag to reorder"
        >
            <GripVertical class="size-5" />
        </div>

        <div class="flex flex-1 items-center gap-3 py-3 pr-3">
            <div
                class="inline-flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary"
            >
                <WidgetIcon
                    :name="meta?.icon ?? 'Square'"
                    fallback="Square"
                    class="size-4"
                />
            </div>
            <div class="flex-1 overflow-hidden">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold">
                        {{ meta?.label ?? widget.type }}
                    </span>
                    <span
                        v-if="!widget.is_active"
                        class="rounded-full bg-muted px-2 py-0.5 text-[10px] font-medium tracking-wide text-muted-foreground uppercase"
                    >
                        Hidden
                    </span>
                    <span
                        v-if="visibilityHidden"
                        class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-300"
                        :title="`Hidden on: ${[visibilityHidden.mobile ? 'mobile' : null, visibilityHidden.tablet ? 'tablet' : null, visibilityHidden.desktop ? 'desktop' : null].filter(Boolean).join(', ')}`"
                    >
                        <Smartphone
                            v-if="visibilityHidden.mobile"
                            class="size-3"
                        />
                        <Tablet v-if="visibilityHidden.tablet" class="size-3" />
                        <Monitor
                            v-if="visibilityHidden.desktop"
                            class="size-3"
                        />
                        hidden
                    </span>
                </div>
                <div
                    v-if="previewText"
                    class="line-clamp-1 text-xs text-muted-foreground"
                >
                    {{ previewText }}
                </div>
            </div>

            <div class="flex items-center gap-1">
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="emit('toggle-active')"
                >
                    <component
                        :is="widget.is_active ? Eye : EyeOff"
                        class="size-4"
                    />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    :title="t('widgets.duplicate')"
                    @click="emit('duplicate')"
                >
                    <Copy class="size-4" />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    :title="t('widgets.copy_to_clipboard')"
                    @click="emit('copy-to-clipboard')"
                >
                    <ClipboardCopy class="size-4" />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    :title="t('widgets.edit_widget')"
                    @click="emit('edit')"
                >
                    <Pencil class="size-4" />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="emit('remove')"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
