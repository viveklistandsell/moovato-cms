<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import { ref } from 'vue';
import type { MenuNode } from './menu-types';

defineOptions({ name: 'HeaderMenuNode' });

defineProps<{
    node: MenuNode;
}>();

const open = ref(false);
</script>

<template>
    <div
        class="relative"
        @mouseenter="open = true"
        @mouseleave="open = false"
    >
        <!-- Leaf link — Inertia <Link> preventDefaults the click and would
             swallow target="_blank", so we use a plain <a> for new-tab links. -->
        <a
            v-if="node.url && node.open_in_new_tab"
            :href="node.url"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center justify-between gap-2 rounded-sm px-3 py-2 text-sm text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
            :class="node.css_class ?? ''"
        >
            <span class="truncate">{{ node.label }}</span>
            <ChevronRight
                v-if="node.children.length > 0"
                class="size-3.5 shrink-0"
            />
        </a>
        <Link
            v-else-if="node.url"
            :href="node.url"
            class="flex items-center justify-between gap-2 rounded-sm px-3 py-2 text-sm text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
            :class="node.css_class ?? ''"
        >
            <span class="truncate">{{ node.label }}</span>
            <ChevronRight
                v-if="node.children.length > 0"
                class="size-3.5 shrink-0"
            />
        </Link>

        <!-- Category / no-URL trigger row -->
        <div
            v-else
            class="flex cursor-default items-center justify-between gap-2 rounded-sm px-3 py-2 text-sm text-muted-foreground transition-colors"
            :class="[
                node.css_class ?? '',
                node.children.length > 0 ? 'hover:bg-muted hover:text-foreground' : '',
            ]"
        >
            <span class="truncate">{{ node.label }}</span>
            <ChevronRight
                v-if="node.children.length > 0"
                class="size-3.5 shrink-0"
            />
        </div>

        <!-- Cascade submenu opens to the right -->
        <div
            v-if="open && node.children.length > 0"
            class="absolute left-full top-0 z-50 ml-1 min-w-[200px] rounded-md border border-border/60 bg-background p-1 shadow-lg"
        >
            <HeaderMenuNode
                v-for="child in node.children"
                :key="child.id"
                :node="child"
            />
        </div>
    </div>
</template>
