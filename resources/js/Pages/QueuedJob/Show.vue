<script setup lang="ts">
import { QueuedJob, Resource } from '@/Constants/Interfaces';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Card from 'primevue/card';

defineProps<{
    queuedJob: Resource<QueuedJob>;
}>();

const topCardProperties = [
    'id',
    'job',
    'queue',
    'attempts',
    'status',
    'created_at',
];
</script>

<template>
    <Head :title="`Job #${queuedJob.data.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Queued Job Details
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <Card>
                    <template #title>Job Information</template>
                    <template #content>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div
                                class="grid grid-cols-12 gap-2 border-b border-gray-100 pb-2"
                                v-for="(property, key) in topCardProperties"
                                :key="`top-card-property-${key}`"
                            >
                                <div
                                    class="col-span-4 font-bold capitalize text-gray-600"
                                >
                                    {{ property.replace('_', ' ') }}
                                </div>
                                <div class="col-span-8 break-all text-gray-900">
                                    {{ queuedJob.data[property] }}
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>

                <Card v-if="queuedJob.data.arguments">
                    <template #title>Arguments</template>
                    <template #content>
                        <div class="flex flex-col gap-2">
                            <div
                                class="grid grid-cols-12 gap-4 border-b border-gray-100 py-2 last:border-0"
                                v-for="(argument, key) in queuedJob.data
                                    .arguments"
                                :key="`queued-job-arg-${key}`"
                            >
                                <div
                                    class="col-span-12 font-mono text-sm text-blue-600 md:col-span-3"
                                >
                                    {{ key }}
                                </div>
                                <div class="col-span-12 md:col-span-9">
                                    <span
                                        v-if="typeof argument === 'object'"
                                        class="font-mono text-xs"
                                    >
                                        {{ JSON.stringify(argument) }}
                                    </span>
                                    <span v-else>
                                        {{ argument }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>

                <Card>
                    <template #title>Console</template>
                    <template #content>
                        <div
                            class="overflow-x-auto rounded-md bg-gray-900 p-4 shadow-inner"
                        >
                            <pre
                                class="whitespace-pre-wrap font-mono text-sm text-green-400"
                                >{{
                                    queuedJob.data.console_output ||
                                    'No output logs available.'
                                }}</pre
                            >
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
