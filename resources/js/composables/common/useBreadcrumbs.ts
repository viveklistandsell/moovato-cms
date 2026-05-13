import {
    inject,
    type InjectionKey,
    provide,
    ref,
    type Ref,
    watch,
    type WatchSource,
} from 'vue';
import type { BreadcrumbItem } from '@/types';

const KEY: InjectionKey<Ref<BreadcrumbItem[]>> = Symbol('breadcrumbs');

/**
 * Layout-side: install a reactive breadcrumbs ref that descendant pages can
 * update via {@link setBreadcrumbs}.
 */
export function provideBreadcrumbs(
    initial: BreadcrumbItem[] = [],
): Ref<BreadcrumbItem[]> {
    const value = ref<BreadcrumbItem[]>(initial);
    provide(KEY, value);
    return value;
}

/**
 * Read the current breadcrumbs ref (set by an ancestor layout).
 */
export function useBreadcrumbs(): Ref<BreadcrumbItem[]> | undefined {
    return inject(KEY, undefined);
}

/**
 * Page-side: set the breadcrumbs displayed in the layout header.
 * Pass a static array, or a getter (e.g. `() => [...]`) for reactive values.
 */
export function setBreadcrumbs(
    source: BreadcrumbItem[] | (() => BreadcrumbItem[]),
): void {
    const breadcrumbs = useBreadcrumbs();
    if (!breadcrumbs) {
        return;
    }

    if (typeof source === 'function') {
        watch(
            source as WatchSource<BreadcrumbItem[]>,
            (value) => {
                breadcrumbs.value = value;
            },
            { immediate: true },
        );
    } else {
        breadcrumbs.value = source;
    }
}
