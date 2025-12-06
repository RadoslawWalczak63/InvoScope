<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Entity } from '@/Constants/Interfaces';

defineProps<{
    entitites: {
        data: Entity[];
    };
}>();
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Entity
            </h2>
        </template>

        <h1>Test</h1>
    </AuthenticatedLayout>
</template>
