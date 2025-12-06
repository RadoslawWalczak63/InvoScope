import { router } from '@inertiajs/vue3';
import { debounce, pickBy } from 'lodash';
import { DataTablePageEvent, DataTableSortEvent } from 'primevue';
import { reactive, ref, watch } from 'vue';

interface UseDataTableProps<T> {
    routeName: string;
    initialFilters: T;
    initialSort?: string;
    initialPerPage?: number;
    initialPage?: number;
}

export function useDataTable<T extends object>({
    routeName,
    initialFilters,
    initialSort = '-created_at',
    initialPerPage = 15,
    initialPage = 1,
}: UseDataTableProps<T>) {
    const loading = ref(false);

    const filters = reactive(initialFilters) as T;

    const rows = ref(initialPerPage);
    const first = ref((initialPage - 1) * rows.value);

    const isDesc = initialSort?.startsWith('-');
    const sortField = ref(initialSort?.replace('-', ''));
    const sortOrder = ref(isDesc ? -1 : 1);

    const fetchData = () => {
        loading.value = true;

        const sortParam = sortField.value
            ? (sortOrder.value === -1 ? '-' : '') + sortField.value
            : null;

        const params = pickBy({
            page: Math.floor(first.value / rows.value) + 1,
            per_page: rows.value,
            sort: sortParam,
            filter: pickBy(filters),
        });

        router.get(route(routeName), params as any, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onFinish: () => (loading.value = false),
        });
    };

    const onPage = (event: DataTablePageEvent) => {
        first.value = event.first;
        rows.value = event.rows;
        fetchData();
    };

    const onSort = (event: DataTableSortEvent) => {
        sortField.value = event.sortField as string;
        sortOrder.value = event.sortOrder as number;
        fetchData();
    };

    watch(
        filters,
        debounce(() => {
            first.value = 0;
            fetchData();
        }, 400),
        { deep: true },
    );

    const clearFilters = () => {
        Object.keys(filters).forEach((key) => {
            // @ts-ignore
            filters[key] = '';
        });
    };

    return {
        loading,
        filters,
        rows,
        first,
        sortField,
        sortOrder,
        onPage,
        onSort,
        clearFilters,
    };
}
