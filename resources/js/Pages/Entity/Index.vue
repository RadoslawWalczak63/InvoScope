<script setup lang="ts">
import { useDataTable } from '@/Composables/useDataTable';
import { Entity, PaginatedResource } from '@/Constants/Interfaces';
import { EntityType } from '@/Enum';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import {
    Button,
    Card,
    Column,
    DataTable,
    FloatLabel,
    InputText,
    Select,
} from 'primevue';
import { ref } from 'vue';
import EntityCreateDialog from './Partials/EntityCreateDialog.vue';

interface EntityFilters {
    type?: string;
    name?: string;
    country?: string;
    email?: string;
}

const props = defineProps<{
    entities: PaginatedResource<Entity>;
    state: {
        filters: EntityFilters;
        sort: string;
    };
}>();

const showCreateModal = ref(false);

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
        country: props.state.filters.country || '',
        email: props.state.filters.email || '',
        name: props.state.filters.name || '',
    },
    initialSort: props.state.sort,
    initialPerPage: props.entities.meta.per_page,
    initialPage: props.entities.meta.current_page,
});
</script>

<template>
    <Head title="Entities" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2
                    class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                >
                    Entities
                </h2>
            </div>
        </template>

        <div class="mb-4 flex justify-end">
            <Button
                label="New Entity"
                icon="pi pi-plus"
                size="small"
                @click="showCreateModal = true"
            />
        </div>

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
                            :options="Object.values(EntityType)"
                            class="w-full"
                            showClear
                        />
                        <label for="type">Type</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <InputText
                            id="name"
                            v-model="filters.name"
                            class="w-full"
                        />
                        <label for="name">Name</label>
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
                    <template #empty>
                        <div class="w-full p-2 text-center">
                            No Entities Found
                        </div>
                    </template>

                    <Column field="type" header="Type" sortable />
                    <Column field="name" header="Name" />
                    <Column field="country" header="Country" sortable />
                    <Column field="email" header="Email" sortable />
                    <Column field="phone" header="Phone" />
                    <Column field="created_at" header="Created At" sortable />
                    <Column>
                        <template #body="slotProps">
                            <Button
                                @click="
                                    router.visit(
                                        route(
                                            'entities.show',
                                            slotProps.data.id,
                                        ),
                                    )
                                "
                                label="View"
                                icon="pi pi-eye"
                                text
                                size="small"
                            />
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <EntityCreateDialog v-model:open="showCreateModal" />
    </AuthenticatedLayout>
</template>
