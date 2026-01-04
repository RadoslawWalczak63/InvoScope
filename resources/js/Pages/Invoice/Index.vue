<script setup lang="ts">
import { useDataTable } from '@/Composables/useDataTable';
import {
    getInvoiceStatusSeverity,
    getInvoiceTypeSeverity,
} from '@/Constants/Helpers';
import { Entity, Invoice, PaginatedResource } from '@/Constants/Interfaces';
import { InvoiceType } from '@/Enum';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InvoiceCreateDialog from '@/Pages/Invoice/Partials/InvoiceCreateDialog.vue';
import InvoiceUploadDialog from '@/Pages/Invoice/Partials/InvoiceUploadDialog.vue';
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

interface InvoiceFilters {
    type?: string;
    number?: string;
    buyer?: string;
    issue_date?: Date;
    due_date?: Date;
    paid_date?: Date;
}

const props = defineProps<{
    invoices: PaginatedResource<Invoice>;
    entities: PaginatedResource<Entity>;
    state: {
        filters: InvoiceFilters;
        sort: string;
    };
    models: Array<{ id: string; name: string }>;
}>();

const showCreateModal = ref(false);
const showUploadModal = ref(false);

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
} = useDataTable<InvoiceFilters>({
    routeName: 'invoices.index',
    initialFilters: {
        type: props.state.filters.type || '',
        number: props.state.filters.number || '',
        buyer: props.state.filters.buyer || '',
        issue_date: props.state.filters.issue_date || undefined,
        due_date: props.state.filters.due_date || undefined,
        paid_date: props.state.filters.paid_date || undefined,
    },
    initialSort: props.state.sort,
    initialPerPage: props.invoices.meta.per_page,
    initialPage: props.invoices.meta.current_page,
});
</script>

<template>
    <Head title="Invoices" />

    <AuthenticatedLayout>
        <template #header>Invoices</template>

        <div class="mb-4 flex justify-end gap-2">
            <Button
                severity="secondary"
                label="Upload Invoice"
                icon="pi pi-file-arrow-up"
                size="small"
                @click="showUploadModal = true"
            />

            <Button
                label="New Invoice"
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
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                    <FloatLabel variant="on">
                        <Select
                            v-model="filters.type"
                            inputId="type"
                            :options="Object.values(InvoiceType)"
                            class="w-full"
                            showClear
                        />
                        <label for="type">Type</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <InputText
                            id="number"
                            v-model="filters.number"
                            class="w-full"
                        />
                        <label for="number">Invoice Number</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <InputText
                            id="buyer"
                            v-model="filters.buyer"
                            class="w-full"
                        />
                        <label for="buyer">Buyer Name</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <DatePicker
                            id="issue_date"
                            showIcon
                            showClear
                            fluid
                            iconDisplay="input"
                            dateFormat="yy-mm-dd"
                            v-model="filters.issue_date"
                        />
                        <label for="issue_date">Issue Date</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <DatePicker
                            id="due_date"
                            showIcon
                            showClear
                            fluid
                            iconDisplay="input"
                            dateFormat="yy-mm-dd"
                            v-model="filters.due_date"
                        />
                        <label for="due_date">Due Date</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <DatePicker
                            id="paid_date"
                            showIcon
                            showClear
                            fluid
                            iconDisplay="input"
                            dateFormat="yy-mm-dd"
                            v-model="filters.paid_date"
                        />
                        <label for="paid_date">Paid Date</label>
                    </FloatLabel>
                </div>
            </template>
        </Card>

        <Card>
            <template #content>
                <DataTable
                    :value="invoices.data"
                    lazy
                    paginator
                    stripedRows
                    :loading="loading"
                    :first="first"
                    :totalRecords="invoices.meta.total"
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
                            No Invoices Found
                        </div>
                    </template>

                    <Column field="number" header="Number" sortable />

                    <Column field="type" header="Type" sortable>
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.type"
                                :severity="
                                    getInvoiceTypeSeverity(slotProps.data.type)
                                "
                                size="small"
                            />
                        </template>
                    </Column>

                    <Column field="type" header="Status" sortable>
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.status"
                                :severity="
                                    getInvoiceStatusSeverity(
                                        slotProps.data.status,
                                    )
                                "
                                size="small"
                            />
                        </template>
                    </Column>

                    <Column header="Buyer">
                        <template #body="slotProps">
                            {{ slotProps.data.buyer?.name || 'N/A' }}
                        </template>
                    </Column>

                    <Column header="Seller">
                        <template #body="slotProps">
                            {{ slotProps.data.seller?.name || 'N/A' }}
                        </template>
                    </Column>

                    <Column field="issue_date" header="Issue Date" sortable />

                    <Column field="created_at" header="Created At" sortable />

                    <Column>
                        <template #body="slotProps">
                            <Button
                                @click="
                                    () => {
                                        router.visit(
                                            route(
                                                'invoices.show',
                                                slotProps.data.id,
                                            ),
                                        );
                                    }
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

        <InvoiceCreateDialog
            :open="showCreateModal"
            :entities="entities.data"
            @update:open="(newVal) => (showCreateModal = newVal)"
        />

        <InvoiceUploadDialog
            v-model:open="showUploadModal"
            :models="models"
            @update:open="(newVal) => (showUploadModal = newVal)"
        />
    </AuthenticatedLayout>
</template>
