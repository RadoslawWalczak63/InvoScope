<script setup lang="ts">
import { useDataTable } from '@/Composables/useDataTable'; // Import the service
import { Entity, PaginatedResponse } from '@/Constants/Interfaces';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Button, Card, Column, DataTable, InputText } from 'primevue';

interface EntityFilters {
    type?: string;
    company_name?: string;
    country?: string;
    email?: string;
}

const props = defineProps<{
    entities: PaginatedResponse<Entity>;
    entityTypes: string[];
    state: {
        filters: EntityFilters;
        sort: string;
    };
}>();

const {
    loading,
    filters,
    rows,
    first,
    sortField,
    sortOrder,
    onPage,
    onSort,
    clearFilters,
} = useDataTable<EntityFilters>({
    routeName: 'entities.index',
    initialFilters: {
        type: props.state.filters.type || '',
        company_name: props.state.filters.company_name || '',
        country: props.state.filters.country || '',
        email: props.state.filters.email || '',
    },
    initialSort: props.state.sort,
    initialPerPage: props.entities.meta.per_page,
    initialPage: props.entities.meta.current_page,
});
</script>

<template>
    <Head title="Entities" />

    <AuthenticatedLayout>
        <template #header>Entities</template>

        <Card class="mb-6">
            <template #title>
                <div class="flex items-center justify-between">
                    <span>Filters</span>
                    <Button
                        label="Clear"
                        icon="pi pi-filter-slash"
                        text
                        size="small"
                        @click="clearFilters"
                        :disabled="loading"
                    />
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <FloatLabel variant="on">
                        <Select
                            v-model="filters.type"
                            inputId="type"
                            :options="entityTypes"
                            class="w-full"
                            variant="filled"
                            showClear
                        />
                        <label for="type">Type</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <InputText
                            id="company"
                            v-model="filters.company_name"
                            class="w-full"
                        />
                        <label for="company">Company</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <InputText
                            id="country"
                            v-model="filters.country"
                            class="w-full"
                        />
                        <label for="country">Country</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <InputText
                            id="email"
                            v-model="filters.email"
                            class="w-full"
                        />
                        <label for="email">Email</label>
                    </FloatLabel>
                </div>
            </template>
        </Card>

        <Card>
            <template #content>
                <DataTable
                    :value="entities.data"
                    lazy
                    paginator
                    stripedRows
                    :loading="loading"
                    :first="first"
                    :totalRecords="entities.meta.total"
                    :rows="rows"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    removableSort
                    @page="onPage"
                    @sort="onSort"
                    :sortField="sortField"
                    :sortOrder="sortOrder"
                    tableStyle="min-width: 50rem"
                >
                    <Column field="type" header="Type" sortable />
                    <Column field="company_name" header="Company" sortable />
                    <Column field="country" header="Country" sortable />
                    <Column field="email" header="Email" sortable />
                    <Column field="phone" header="Phone" />
                    <Column field="created_at" header="Created At" sortable />
                </DataTable>
            </template>
        </Card>
    </AuthenticatedLayout>
</template>
