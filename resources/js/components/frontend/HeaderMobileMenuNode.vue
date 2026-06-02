<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import type { MenuNode } from './menu-types';

defineOptions({ name: 'HeaderMobileMenuNode' });

const props = withDefaults(
    defineProps<{
        node: MenuNode;
        depth?: number;
    }>(),
    { depth: 0 },
);

const emit = defineEmits<{ navigate: [] }>();

const open = ref(false);

const hasChildren = computed<boolean>(() => props.node.children.length > 0);

// Cap the visual indent so very deep trees stay readable on a phone.
const indentStyle = computed<string>(() =>
    props.depth === 0
        ? ''
        : `padding-left: ${Math.min(props.depth, 4) * 0.75 + 0.5}rem`,
);

function toggle(): void {
    open.value = !open.value;
}

function onLabelClick(): void {
    if (hasChildren.value) {
        toggle();
    }
}
</script>

<template>
    <div>
        <div
            class="flex items-center gap-1 rounded-md hover:bg-muted"
            :class="node.css_class ?? ''"
            :style="indentStyle"
        >
            <!-- New-tab links: plain <a>, Inertia's <Link> swallows target. -->
            <a
                v-if="node.url && node.open_in_new_tab"
                :href="node.url"
                target="_blank"
                rel="noopener noreferrer"
                class="flex-1 py-2 pr-2 text-sm"
                @click="emit('navigate')"
            >
                {{ node.label }}
            </a>
            <Link
                v-else-if="node.url"
                :href="node.url"
                class="flex-1 py-2 pr-2 text-sm"
                @click="emit('navigate')"
            >
                {{ node.label }}
            </Link>
            <button
                v-else
                type="button"
                class="flex-1 py-2 pr-2 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground"
                @click="onLabelClick"
            >
                {{ node.label }}
            </button>

            <!-- Expand toggle — only when this node has children. Sits to the
                 right so the link stays the primary tap target. -->
            <button
                v-if="hasChildren"
                type="button"
                class="ml-auto mr-1 inline-flex size-8 items-center justify-center rounded-md text-muted-foreground hover:bg-muted-foreground/10"
                :aria-expanded="open"
                :aria-label="open ? `Collapse ${node.label}` : `Expand ${node.label}`"
                @click.stop="toggle"
            >
                <ChevronDown
                    class="size-4 transition-transform duration-150"
                    :class="open ? 'rotate-180' : ''"
                />
            </button>
        </div>

        <div v-if="hasChildren && open">
            <HeaderMobileMenuNode
                v-for="child in node.children"
                :key="child.id"
                :node="child"
                :depth="depth + 1"
                @navigate="emit('navigate')"
            />
        </div>
    </div>
</template>
