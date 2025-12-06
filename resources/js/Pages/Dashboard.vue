<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>Dashboard</template>

        <div
            class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg"
        >
            <div class="p-6 text-gray-900 dark:text-gray-100">
                You're logged in!
            </div>
        </div>
    </AuthenticatedLayout>
</template>
