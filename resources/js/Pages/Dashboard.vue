<script setup lang="ts">
import { InvoiceStatus, InvoiceType } from '@/Enum';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Card from 'primevue/card';
import Chart from 'primevue/chart';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import { computed, ref, watch } from 'vue';

interface DashboardProps {
    kpis: {
        income: number;
        expense: number;
        profit: number;
    };
    chart: {
        labels: string[];
        income: number[];
        expense: number[];
        net_cumulative: number[];
    };
    monthlyChart: {
        labels: string[];
        income: number[];
        expense: number[];
    };
    expenseBreakdown: {
        labels: string[];
        data: number[];
    };
    recentInvoices: any[];
    filters: {
        currency: string;
        currencies: string[];
        startDate: string;
        endDate: string;
    };
}

const props = defineProps<DashboardProps>();

const dates = ref([
    new Date(props.filters.startDate),
    new Date(props.filters.endDate),
]);
const selectedCurrency = ref(props.filters.currency);

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: props.filters.currency,
        maximumFractionDigits: 0,
    }).format(value);
};

watch(
    () => [selectedCurrency.value, dates.value],
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

const mainChartData = computed(() => ({
    labels: props.chart.labels,
    datasets: [
        {
            label: 'Net Balance',
            data: props.chart.net_cumulative,
            fill: true,
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            yAxisID: 'y',
        },
        {
            type: 'bar',
            label: 'Daily Expense',
            data: props.chart.expense,
            backgroundColor: '#F87171',
            borderRadius: 4,
            yAxisID: 'y1',
        },
    ],
}));

const mainChartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: 'index',
        intersect: false,
    },
    plugins: {
        legend: { position: 'top', align: 'end' },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { maxTicksLimit: 10 },
        },
        y: {
            type: 'linear',
            display: true,
            position: 'left',
            grid: { borderDash: [4, 4], color: '#f3f4f6' },
            title: { display: true, text: 'Cumulative' },
        },
        y1: {
            type: 'linear',
            display: false,
            position: 'right',
            grid: { display: false },
            min: 0,
        },
    },
});

const monthlyBarData = computed(() => ({
    labels: props.monthlyChart.labels,
    datasets: [
        {
            label: 'Income',
            backgroundColor: '#10B981',
            data: props.monthlyChart.income,
            borderRadius: 4,
        },
        {
            label: 'Expense',
            backgroundColor: '#EF4444',
            data: props.monthlyChart.expense,
            borderRadius: 4,
        },
    ],
}));

const monthlyBarOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' },
    },
    scales: {
        x: { grid: { display: false } },
        y: { grid: { color: '#f3f4f6' } },
    },
});

const expenseDonutData = computed(() => ({
    labels: props.expenseBreakdown.labels,
    datasets: [
        {
            data: props.expenseBreakdown.data,
            backgroundColor: [
                '#3B82F6',
                '#F59E0B',
                '#10B981',
                '#6366F1',
                '#EC4899',
            ],
            hoverOffset: 4,
        },
    ],
}));

const expenseDonutOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right',
            labels: { boxWidth: 12, usePointStyle: true },
        },
    },
});
</script>

<template>
    <Head title="Financial Overview" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Financial Overview
                </h2>

                <div class="flex flex-col items-center gap-3 sm:flex-row">
                    <div class="w-full sm:w-40">
                        <Select
                            v-model="selectedCurrency"
                            :options="props.filters.currencies"
                            placeholder="Currency"
                            class="w-full"
                        />
                    </div>
                    <div class="w-full sm:w-64">
                        <DatePicker
                            v-model="dates"
                            selectionMode="range"
                            dateFormat="yy-mm-dd"
                            placeholder="Select Range"
                            showIcon
                            class="w-full"
                            size="small"
                            :manualInput="false"
                        />
                    </div>
                </div>
            </div>
        </template>

        <div class="space-y-6 py-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <Card
                    class="relative overflow-hidden border-l-4 border-emerald-500 shadow-sm"
                >
                    <template #content>
                        <div
                            class="relative z-10 flex items-start justify-between"
                        >
                            <div>
                                <p
                                    class="text-sm font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Net Profit
                                </p>
                                <h3
                                    class="mt-2 text-3xl font-bold text-gray-800"
                                >
                                    {{ formatCurrency(props.kpis.profit) }}
                                </h3>
                            </div>
                            <div class="rounded-lg bg-emerald-50 p-2">
                                <i
                                    class="pi pi-wallet text-xl text-emerald-600"
                                ></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="border-l-4 border-blue-500 shadow-sm">
                    <template #content>
                        <div class="flex items-start justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Total Income
                                </p>
                                <h3
                                    class="mt-2 text-3xl font-bold text-gray-800"
                                >
                                    {{ formatCurrency(props.kpis.income) }}
                                </h3>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-2">
                                <i
                                    class="pi pi-arrow-up-right text-xl text-blue-600"
                                ></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="border-l-4 border-orange-500 shadow-sm">
                    <template #content>
                        <div class="flex items-start justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Total Expenses
                                </p>
                                <h3
                                    class="mt-2 text-3xl font-bold text-gray-800"
                                >
                                    {{ formatCurrency(props.kpis.expense) }}
                                </h3>
                            </div>
                            <div class="rounded-lg bg-orange-50 p-2">
                                <i
                                    class="pi pi-arrow-down-right text-xl text-orange-600"
                                ></i>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <Card class="shadow-sm lg:col-span-2">
                    <template #title>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-gray-700">
                                Financial Trend
                            </span>
                            <Tag
                                value="Net Cumulative"
                                severity="info"
                                class="text-xs"
                            />
                        </div>
                    </template>
                    <template #content>
                        <div class="mt-4 h-[350px] w-full">
                            <Chart
                                type="line"
                                :data="mainChartData"
                                :options="mainChartOptions"
                                class="h-full w-full"
                            />
                        </div>
                    </template>
                </Card>

                <Card class="flex flex-col shadow-sm">
                    <template #title>
                        <span class="text-lg font-bold text-gray-700">
                            Recent Activity
                        </span>
                    </template>
                    <template #content>
                        <div class="-mx-4">
                            <DataTable
                                :value="props.recentInvoices"
                                :rows="6"
                                responsiveLayout="scroll"
                                size="small"
                                class="text-sm"
                            >
                                <Column field="client_name" header="Party">
                                    <template #body="slotProps">
                                        <div class="flex flex-col">
                                            <span
                                                class="max-w-[120px] truncate font-medium"
                                                :title="
                                                    slotProps.data.client_name
                                                "
                                            >
                                                {{ slotProps.data.client_name }}
                                            </span>
                                            <span
                                                class="text-[10px] text-gray-400"
                                            >
                                                #{{ slotProps.data.number }}
                                            </span>
                                        </div>
                                    </template>
                                </Column>

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
                                            class="font-bold"
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

                                <Column header="" style="width: 10%">
                                    <template #body="slotProps">
                                        <div
                                            class="h-2 w-2 rounded-full"
                                            :class="{
                                                'bg-green-500':
                                                    slotProps.data.status ===
                                                    InvoiceStatus.PAID,
                                                'bg-gray-300':
                                                    slotProps.data.status ===
                                                    InvoiceStatus.DRAFT,
                                                'bg-red-500':
                                                    slotProps.data.status ===
                                                    InvoiceStatus.OVERDUE,
                                                'bg-orange-400':
                                                    slotProps.data.status ===
                                                    InvoiceStatus.SENT,
                                            }"
                                            :title="slotProps.data.status"
                                        ></div>
                                    </template>
                                </Column>
                                <template #empty>
                                    <div
                                        class="p-4 text-center text-xs text-gray-500"
                                    >
                                        No transactions found.
                                    </div>
                                </template>
                            </DataTable>
                        </div>
                    </template>
                </Card>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <Card class="shadow-sm">
                    <template #title>
                        <span class="text-lg font-bold text-gray-700"
                            >Monthly Volume</span
                        >
                    </template>
                    <template #content>
                        <div class="mt-2 flex h-[300px] w-full justify-center">
                            <Chart
                                type="bar"
                                :data="monthlyBarData"
                                :options="monthlyBarOptions"
                                class="h-full w-full"
                            />
                        </div>
                    </template>
                </Card>

                <Card class="shadow-sm">
                    <template #title>
                        <span class="text-lg font-bold text-gray-700"
                            >Top Expenses by Entity</span
                        >
                    </template>
                    <template #content>
                        <div class="mt-2 flex h-[300px] w-full justify-center">
                            <Chart
                                type="doughnut"
                                :data="expenseDonutData"
                                :options="expenseDonutOptions"
                                class="h-full w-full"
                            />
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
:deep(.p-datepicker-input) {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    font-size: 0.875rem;
}
</style>
