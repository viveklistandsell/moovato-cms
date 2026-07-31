<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { useT } from '@/composables/useT';

const t = useT();

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type PaginationMeta = {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
};

const props = withDefaults(
    defineProps<{
        pagination: PaginationMeta;
        only?: string[];
    }>(),
    {
        only: () => [],
    },
);

const onlyOption = computed<string[] | undefined>(() =>
    props.only.length > 0 ? props.only : undefined,
);

function isPrev(label: string): boolean {
    return label.toLowerCase().includes('prev') || label.includes('&laquo;');
}

function isNext(label: string): boolean {
    return label.toLowerCase().includes('next') || label.includes('&raquo;');
}

function cleanLabel(label: string): string {
    return label.replace(/&laquo;|&raquo;|Previous|Next/gi, '').trim();
}
</script>

<template>
    <div
        class="flex flex-wrap items-center justify-between gap-3 border-t border-sidebar-border/70 px-4 py-3 text-sm dark:border-sidebar-border"
    >
        <p class="text-xs text-muted-foreground">
            <template v-if="pagination.total > 0">
                {{
                    t('table.pagination_showing', {
                        from: pagination.from ?? 0,
                        to: pagination.to ?? 0,
                        total: pagination.total,
                    })
                }}
                <span v-if="pagination.last_page > 1" class="ml-2">
                    {{
                        t('table.pagination_page', {
                            current: pagination.current_page,
                            last: pagination.last_page,
                        })
                    }}
                </span>
            </template>
            <template v-else>{{ t('table.pagination_no_results') }}</template>
        </p>

        <div v-if="pagination.last_page > 1" class="flex items-center gap-1">
            <template v-for="(link, idx) in pagination.links" :key="idx">
                <template v-if="isPrev(link.label) || isNext(link.label)">
                    <Button
                        v-if="!link.url"
                        variant="outline"
                        size="sm"
                        disabled
                        class="h-8 px-2"
                    >
                        <ChevronLeft v-if="isPrev(link.label)" class="size-4" />
                        <ChevronRight v-else class="size-4" />
                    </Button>
                    <Button
                        v-else
                        as-child
                        variant="outline"
                        size="sm"
                        class="h-8 px-2"
                    >
                        <Link
                            :href="link.url"
                            :only="onlyOption"
                            preserve-scroll
                            preserve-state
                            replace
                        >
                            <ChevronLeft
                                v-if="isPrev(link.label)"
                                class="size-4"
                            />
                            <ChevronRight v-else class="size-4" />
                        </Link>
                    </Button>
                </template>

                <template v-else>
                    <Button
                        v-if="!link.url || link.active"
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        :disabled="!link.url || link.active"
                        class="h-8 min-w-8 px-2"
                    >
                        <span v-html="cleanLabel(link.label) || link.label" />
                    </Button>
                    <Button
                        v-else
                        as-child
                        variant="outline"
                        size="sm"
                        class="h-8 min-w-8 px-2"
                    >
                        <Link
                            :href="link.url"
                            :only="onlyOption"
                            preserve-scroll
                            preserve-state
                            replace
                        >
                            <span
                                v-html="cleanLabel(link.label) || link.label"
                            />
                        </Link>
                    </Button>
                </template>
            </template>
        </div>
    </div>
</template>
