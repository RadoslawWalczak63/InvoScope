<script setup lang="ts">
import { getInvoiceStatusSeverity } from '@/Constants/Helpers';
import { Currency, InvoiceType } from '@/Enum';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { FloatLabel } from 'primevue';
import Card from 'primevue/card';
import Chart from 'primevue/chart';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    stats: {
        income: number;
        expense: number;
        profit: number;
        overdue: number;
    };
    charts: {
        monthly: { labels: string[]; income: number[]; expense: number[] };
        expenses: { labels: string[]; data: number[] };
        status: { labels: string[]; data: number[] };
    };
    recentInvoices: any[];
    selectedCurrency: string;
    filters: {
        startDate: string;
        endDate: string;
    };
}>();

const dates = ref([
    new Date(props.filters.startDate),
    new Date(props.filters.endDate),
]);
const form = ref({
    currency: props.selectedCurrency,
    dates: dates,
});

watch(
    () => [form.value.currency, form.value.dates],
    ([newCurrency, newDates]) => {
        if (Array.isArray(newDates) && newDates[0] && newDates[1]) {
            router.get(
                route('dashboard'),
                {
                    currency: newCurrency,
                    startDate: newDates[0].toISOString().split('T')[0],
                    endDate: newDates[1].toISOString().split('T')[0],
                },
                { preserveState: true, replace: true },
            );
        }
    },
    { deep: true },
);

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: form.value.currency,
    }).format(value);
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
    scales: { r: { ticks: { display: false }, grid: { color: '#F3F4F6' } } },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>Dashboard</template>

        <div class="space-y-6">
            <Card>
                <template #title>Filters</template>
                <template #content>
                    <div class="mt-2 grid grid-cols-2 gap-4">
                        <FloatLabel variant="on">
                            <DatePicker
                                id="period"
                                v-model="form.dates"
                                showIcon
                                showClear
                                fluid
                                iconDisplay="input"
                                dateFormat="yy-mm-dd"
                                selectionMode="range"
                                :manualInput="false"
                                size="small"
                            />
                            <label for="period">Date Range</label>
                        </FloatLabel>

                        <FloatLabel variant="on">
                            <Select
                                id="currency"
                                v-model="form.currency"
                                :options="Object.values(Currency)"
                                size="small"
                                filter
                                fluid
                            />
                            <label for="currency">Currency</label>
                        </FloatLabel>
                    </div>
                </template>
            </Card>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card v-for="(val, label) in stats" :key="label">
                    <template #content>
                        <p
                            class="mb-1 text-xs font-bold uppercase tracking-widest text-gray-400"
                        >
                            {{ label }}
                        </p>
                        <p
                            class="text-2xl font-black"
                            :class="{
                                'text-orange-500': label === 'overdue',
                                'text-emerald-500': label === 'profit',
                            }"
                        >
                            {{ formatCurrency(val) }}
                        </p>
                    </template>
                </Card>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="flex flex-col gap-6 lg:col-span-2">
                    <Card class="shadow-sm">
                        <template #title>Financial Performance</template>
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
                        <template #title>Recent Transactions</template>
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
                                            :class="{
                                                'text-emerald-600':
                                                    slotProps.data.type ===
                                                    InvoiceType.INCOME,
                                                'text-red-600':
                                                    slotProps.data.type ===
                                                    InvoiceType.EXPENSE,
                                            }"
                                            class="text-sm font-bold"
                                        >
                                            {{
                                                slotProps.data.type ===
                                                InvoiceType.INCOME
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
                                                getInvoiceStatusSeverity(
                                                    slotProps.data.status,
                                                )
                                            "
                                        />
                                    </template>
                                </Column>
                                <template #empty>
                                    <div class="py-6 text-center text-gray-500">
                                        No recent transactions found.
                                    </div>
                                </template>
                            </DataTable>
                        </template>
                    </Card>
                </div>

                <div class="flex flex-col gap-6">
                    <Card class="shadow-sm">
                        <template #title>Top Expense Categories</template>
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
                        <template #title>Transaction Volume</template>
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
