<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Chart from 'primevue/chart';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import { computed, ref } from 'vue';

const props = defineProps<{
    stats: {
        income: number;
        expense: number;
        profit: number;
        active_clients: number;
    };
    charts: {
        monthly: { labels: string[]; income: number[]; expense: number[] };
        expenses: { labels: string[]; data: number[] };
        status: { labels: string[]; data: number[] };
    };
    recentInvoices: any[];
}>();

const selectedPeriod = ref('Last 6 Months');
const periods = ref(['Last 6 Months', 'Year to Date']);

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

const getSeverity = (status: string) => {
    switch (status.toLowerCase()) {
        case 'paid':
            return 'success';
        case 'pending':
            return 'warn';
        case 'overdue':
            return 'danger';
        default:
            return 'secondary';
    }
};

const barData = computed(() => ({
    labels: props.charts.monthly.labels,
    datasets: [
        {
            label: 'Income',
            backgroundColor: '#10B981',
            data: props.charts.monthly.income,
            borderRadius: 6,
            barPercentage: 0.6,
            categoryPercentage: 0.8,
        },
        {
            label: 'Expense',
            backgroundColor: '#EF4444',
            data: props.charts.monthly.expense,
            borderRadius: 6,
            barPercentage: 0.6,
            categoryPercentage: 0.8,
        },
    ],
}));

const barOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
            align: 'end',
            labels: { usePointStyle: true, boxWidth: 8 },
        },
        tooltip: { mode: 'index', intersect: false },
    },
    scales: {
        x: { grid: { display: false }, ticks: { color: '#6B7280' } },
        y: {
            grid: { color: '#F3F4F6', borderDash: [5, 5] },
            border: { display: false },
            ticks: { color: '#6B7280' },
        },
    },
});

const doughnutData = computed(() => ({
    labels: props.charts.expenses.labels,
    datasets: [
        {
            data: props.charts.expenses.data,
            backgroundColor: [
                '#3B82F6',
                '#F59E0B',
                '#10B981',
                '#6366F1',
                '#EC4899',
            ],
            borderWidth: 0,
            hoverOffset: 10,
        },
    ],
}));

const doughnutOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    cutout: '75%',
    plugins: {
        legend: {
            position: 'bottom',
            labels: { usePointStyle: true, padding: 20 },
        },
    },
});

const polarData = computed(() => ({
    labels: props.charts.status.labels,
    datasets: [
        {
            data: props.charts.status.data,
            backgroundColor: [
                'rgba(16, 185, 129, 0.7)',
                'rgba(239, 68, 68, 0.7)',
            ],
            borderWidth: 0,
        },
    ],
}));

const polarOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom', labels: { usePointStyle: true } },
    },
    scales: {
        r: {
            ticks: { display: false },
            grid: { color: '#F3F4F6' },
        },
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Executive Overview
            </h2>
        </template>

        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <Card class="border-l-4 border-emerald-500 shadow-sm">
                    <template #content>
                        <div class="flex h-full flex-col justify-between">
                            <span
                                class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-400"
                                >Total Income</span
                            >
                            <div
                                class="text-3xl font-bold text-gray-800 dark:text-white"
                            >
                                {{ formatCurrency(props.stats.income) }}
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="border-l-4 border-red-500 shadow-sm">
                    <template #content>
                        <div class="flex h-full flex-col justify-between">
                            <span
                                class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-400"
                                >Total Expenses</span
                            >
                            <div
                                class="text-3xl font-bold text-gray-800 dark:text-white"
                            >
                                {{ formatCurrency(props.stats.expense) }}
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="border-l-4 border-indigo-500 shadow-sm">
                    <template #content>
                        <div class="flex h-full flex-col justify-between">
                            <span
                                class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-400"
                                >Net Profit</span
                            >
                            <div
                                class="text-3xl font-bold text-gray-800 dark:text-white"
                            >
                                {{ formatCurrency(props.stats.profit) }}
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="border-l-4 border-orange-500 shadow-sm">
                    <template #content>
                        <div class="flex h-full flex-col justify-between">
                            <span
                                class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-400"
                                >Active Clients</span
                            >
                            <div class="flex items-end justify-between">
                                <div
                                    class="text-3xl font-bold text-gray-800 dark:text-white"
                                >
                                    {{ props.stats.active_clients }}
                                </div>
                                <Tag
                                    severity="success"
                                    icon="pi pi-arrow-up"
                                    value="4 new"
                                    rounded
                                ></Tag>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="flex flex-col gap-6 lg:col-span-2">
                    <Card class="shadow-sm">
                        <template #title>
                            <div class="mb-4 flex items-center justify-between">
                                <span
                                    class="text-lg font-bold text-gray-700 dark:text-gray-300"
                                    >Financial Performance</span
                                >
                                <Select
                                    v-model="selectedPeriod"
                                    :options="periods"
                                    placeholder="Select Range"
                                    size="small"
                                />
                            </div>
                        </template>
                        <template #content>
                            <div class="h-[350px] w-full">
                                <Chart
                                    type="bar"
                                    :data="barData"
                                    :options="barOptions"
                                    class="h-full w-full"
                                />
                            </div>
                        </template>
                    </Card>

                    <Card class="overflow-hidden shadow-sm">
                        <template #title>
                            <div class="mb-2 flex items-center justify-between">
                                <span
                                    class="text-lg font-bold text-gray-700 dark:text-gray-300"
                                    >Recent Transactions</span
                                >
                                <Button
                                    icon="pi pi-ellipsis-h"
                                    text
                                    rounded
                                    aria-label="Menu"
                                    class="text-gray-400"
                                />
                            </div>
                        </template>
                        <template #content>
                            <DataTable
                                :value="props.recentInvoices"
                                :rows="5"
                                responsiveLayout="scroll"
                                stripedRows
                                tableStyle="min-width: 50rem"
                            >
                                <Column field="number" header="ID">
                                    <template #body="slotProps">
                                        <span
                                            class="font-mono text-xs text-gray-500"
                                            >#{{ slotProps.data.number }}</span
                                        >
                                    </template>
                                </Column>
                                <Column
                                    field="client_name"
                                    header="Counterparty"
                                >
                                    <template #body="slotProps">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium">{{
                                                slotProps.data.client_name
                                            }}</span>
                                            <span
                                                class="text-xs capitalize text-gray-400"
                                                >{{ slotProps.data.type }}</span
                                            >
                                        </div>
                                    </template>
                                </Column>
                                <Column field="issue_date" header="Date" />
                                <Column header="Amount" class="text-right">
                                    <template #body="slotProps">
                                        <span
                                            :class="
                                                slotProps.data.type === 'income'
                                                    ? 'text-emerald-600'
                                                    : 'text-red-600'
                                            "
                                            class="text-sm font-bold"
                                        >
                                            {{
                                                slotProps.data.type === 'income'
                                                    ? '+'
                                                    : '-'
                                            }}
                                            {{
                                                formatCurrency(
                                                    slotProps.data.amount,
                                                )
                                            }}
                                        </span>
                                    </template>
                                </Column>
                                <Column header="Status">
                                    <template #body="slotProps">
                                        <Tag
                                            :value="slotProps.data.status"
                                            :severity="
                                                getSeverity(
                                                    slotProps.data.status,
                                                )
                                            "
                                        />
                                    </template>
                                </Column>
                            </DataTable>
                        </template>
                    </Card>
                </div>

                <div class="flex flex-col gap-6">
                    <Card class="shadow-sm">
                        <template #title>
                            <span
                                class="text-sm font-bold uppercase tracking-wider text-gray-500"
                                >Top Expense Categories</span
                            >
                        </template>
                        <template #content>
                            <div
                                class="mt-4 flex h-[250px] w-full justify-center"
                            >
                                <Chart
                                    type="doughnut"
                                    :data="doughnutData"
                                    :options="doughnutOptions"
                                    class="h-full w-full"
                                />
                            </div>
                        </template>
                    </Card>

                    <Card class="flex-grow shadow-sm">
                        <template #title>
                            <span
                                class="text-sm font-bold uppercase tracking-wider text-gray-500"
                                >Transaction Volume</span
                            >
                        </template>
                        <template #content>
                            <div
                                class="mt-4 flex h-[250px] w-full justify-center"
                            >
                                <Chart
                                    type="polarArea"
                                    :data="polarData"
                                    :options="polarOptions"
                                    class="h-full w-full"
                                />
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
