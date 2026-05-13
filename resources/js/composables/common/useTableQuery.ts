import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

export type TableFilters = {
    q?: string | null;
    sort_by?: string | null;
    sort_dir?: 'asc' | 'desc';
    per_page?: number;
};

type UseTableQueryOptions = {
    /**
     * Inertia partial-reload prop names. The server will only re-compute and
     * re-send these props on filter/sort/page changes — leaves expensive
     * unchanged props (languages, parent options, etc.) alone.
     */
    only?: string[];
};

/**
 * Manages table query state (search, sort, page) with Inertia v3 partial
 * reloads + async/await. Each mutation returns a Promise that resolves when
 * the server round-trip completes — consumers can `await` it.
 *
 * Exposes `isLoading` ref so UIs can show a spinner during the request.
 */
export function useTableQuery(
    routeUrl: string,
    initial: TableFilters,
    options: UseTableQueryOptions = {},
) {
    const search = ref<string>(initial.q ?? '');
    const sortBy = ref<string | null>(initial.sort_by ?? null);
    const sortDir = ref<'asc' | 'desc'>(initial.sort_dir ?? 'asc');
    const perPage = ref<number>(initial.per_page ?? 10);
    const isLoading = ref(false);

    function buildParams(
        overrides: Partial<TableFilters & { page: number }> = {},
    ): Record<string, string | number> {
        const params: Record<string, string | number> = {};
        const q = overrides.q !== undefined ? overrides.q : search.value;
        const sb =
            overrides.sort_by !== undefined ? overrides.sort_by : sortBy.value;
        const sd = overrides.sort_dir ?? sortDir.value;
        const pp = overrides.per_page ?? perPage.value;
        const page = overrides.page;

        if (q) {
            params.q = q;
        }
        if (sb) {
            params.sort_by = sb;
            params.sort_dir = sd;
        }
        if (pp && pp !== 10) {
            params.per_page = pp;
        }
        if (page && page > 1) {
            params.page = page;
        }
        return params;
    }

    function applyQuery(
        overrides: Partial<TableFilters & { page: number }> = {},
    ): Promise<void> {
        return new Promise<void>((resolve) => {
            router.get(routeUrl, buildParams(overrides), {
                only: options.only,
                preserveScroll: true,
                preserveState: true,
                replace: true,
                onStart: () => {
                    isLoading.value = true;
                },
                onFinish: () => {
                    isLoading.value = false;
                    resolve();
                },
            });
        });
    }

    async function setSearch(value: string): Promise<void> {
        search.value = value;
        await applyQuery({ q: value, page: 1 });
    }

    async function toggleSort(column: string): Promise<void> {
        if (sortBy.value === column) {
            sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
        } else {
            sortBy.value = column;
            sortDir.value = 'asc';
        }
        await applyQuery({
            sort_by: sortBy.value,
            sort_dir: sortDir.value,
            page: 1,
        });
    }

    async function clearSort(): Promise<void> {
        sortBy.value = null;
        await applyQuery({ sort_by: null, page: 1 });
    }

    async function goToPage(page: number): Promise<void> {
        await applyQuery({ page });
    }

    async function setPerPage(value: number): Promise<void> {
        perPage.value = value;
        await applyQuery({ per_page: value, page: 1 });
    }

    async function resetAll(): Promise<void> {
        search.value = '';
        sortBy.value = null;
        sortDir.value = 'asc';
        perPage.value = initial.per_page ?? 10;
        return new Promise<void>((resolve) => {
            router.get(
                routeUrl,
                {},
                {
                    only: options.only,
                    preserveScroll: true,
                    preserveState: true,
                    replace: true,
                    onStart: () => {
                        isLoading.value = true;
                    },
                    onFinish: () => {
                        isLoading.value = false;
                        resolve();
                    },
                },
            );
        });
    }

    return {
        search,
        sortBy,
        sortDir,
        perPage,
        isLoading,
        setSearch,
        toggleSort,
        clearSort,
        goToPage,
        setPerPage,
        resetAll,
    };
}
