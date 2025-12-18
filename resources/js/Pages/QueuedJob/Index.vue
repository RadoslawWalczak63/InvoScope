<script setup lang="ts">
import { useDataTable } from '@/Composables/useDataTable';
import { getQueuedJobSeverity } from '@/Constants/Helpers';
import { PaginatedResource, QueuedJob } from '@/Constants/Interfaces';
import { QueuedJobStatus } from '@/Enum';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import {
    Button,
    Card,
    Column,
    DataTable,
    FloatLabel,
    InputText,
    Tag,
} from 'primevue';

interface JobFilters {
    job?: string;
    status?: string;
}

const props = defineProps<{
    queuedJobs: PaginatedResource<QueuedJob>;
    state: {
        filters: JobFilters;
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
} = useDataTable<JobFilters>({
    routeName: 'queued-jobs.index',
    initialFilters: {
        job: props.state.filters.job || '',
        status: props.state.filters.status || '',
    },
    initialSort: props.state.sort,
    initialPerPage: props.queuedJobs.meta.per_page,
    initialPage: props.queuedJobs.meta.current_page,
});
</script>

<template>
    <Head title="Queued Jobs" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2
                    class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                >
                    Queued Jobs
                </h2>
            </div>
        </template>

        <Card class="mb-6 mt-4">
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
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <FloatLabel variant="on">
                        <InputText
                            id="job_class"
                            v-model="filters.job"
                            class="w-full"
                        />
                        <label for="job_class">Job</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <Select
                            id="status"
                            v-model="filters.status"
                            :options="Object.values(QueuedJobStatus)"
                            class="w-full"
                            showClear
                        />
                        <label for="status">Status</label>
                    </FloatLabel>
                </div>
            </template>
        </Card>

        <Card>
            <template #content>
                <DataTable
                    :value="queuedJobs.data"
                    lazy
                    paginator
                    stripedRows
                    :loading="loading"
                    :first="first"
                    :totalRecords="queuedJobs.meta.total"
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
                        <div class="w-full p-2 text-center">No Jobs Found</div>
                    </template>

                    <Column
                        field="job"
                        header="Job"
                        sortable
                        class="font-medium"
                    />

                    <Column field="status" header="Status" sortable>
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.status"
                                :severity="
                                    getQueuedJobSeverity(slotProps.data.status)
                                "
                            />
                        </template>
                    </Column>

                    <Column field="started_at" header="Started At" sortable />
                    <Column field="finished_at" header="Finished At" sortable />
                    <Column field="created_at" header="Queued At" sortable />

                    <Column>
                        <template #body="slotProps">
                            <Button
                                @click="
                                    router.visit(
                                        route(
                                            'queued-jobs.show',
                                            slotProps.data.id,
                                        ),
                                    )
                                "
                                label="Details"
                                icon="pi pi-list"
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
