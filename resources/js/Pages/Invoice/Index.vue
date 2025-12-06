<script setup lang="ts">
import { useDataTable } from '@/Composables/useDataTable';
import { Invoice, PaginatedResource } from '@/Constants/Interfaces';
import { InvoiceStatus, InvoiceType } from '@/Enum';
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

interface InvoiceFilters {
    type?: string;
    number?: string;
    buyer?: string;
    issue_date?: string;
}

const props = defineProps<{
    invoices: PaginatedResource<Invoice>;
    invoiceTypes: string[];
    state: {
        filters: InvoiceFilters;
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
} = useDataTable<InvoiceFilters>({
    routeName: 'invoices.index',
    initialFilters: {
        type: props.state.filters.type || '',
        number: props.state.filters.number || '',
        buyer: props.state.filters.buyer || '',
        issue_date: props.state.filters.issue_date || '',
    },
    initialSort: props.state.sort,
    initialPerPage: props.invoices.meta.per_page,
    initialPage: props.invoices.meta.current_page,
});

const getStatusSeverity = (status: string) => {
    switch (status) {
        case InvoiceStatus.DRAFT:
            return 'secondary';
        case InvoiceStatus.SENT:
            return 'warning';
        case InvoiceStatus.PAID:
            return 'success';
        case InvoiceStatus.OVERDUE:
            return 'danger';
    }
};

const getTypeSeverity = (type: string) => {
    switch (type) {
        case InvoiceType.EXPENSE:
            return 'warn';
        case InvoiceType.INCOME:
            return 'success';
    }
};
</script>

<template>
    <Head title="Invoices" />

    <AuthenticatedLayout>
        <template #header>Invoices</template>

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
                            :options="invoiceTypes"
                            class="w-full"
                            variant="filled"
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
                        <InputText
                            id="issue_date"
                            type="date"
                            v-model="filters.issue_date"
                            class="w-full"
                        />
                        <label for="issue_date">Issue Date</label>
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
                    <Column field="number" header="Number" sortable />

                    <Column field="type" header="Type" sortable>
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.type"
                                :severity="getTypeSeverity(slotProps.data.type)"
                                size="small"
                            />
                        </template>
                    </Column>

                    <Column field="type" header="Status" sortable>
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.status"
                                :severity="
                                    getStatusSeverity(slotProps.data.status)
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
    </AuthenticatedLayout>
</template>
