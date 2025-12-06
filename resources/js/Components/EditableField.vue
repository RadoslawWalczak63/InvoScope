<script setup lang="ts">
defineProps<{
    label: string;
    isEditing?: boolean;
    fullWidth?: boolean;
}>();
</script>

<template>
    <div :class="['space-y-1', fullWidth ? 'md:col-span-2' : '']">
        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
            {{ label }}
        </label>

        <div v-if="isEditing" class="fade-in">
            <slot name="input" />
            <small v-if="$slots.error" class="mt-1 block text-xs text-red-500">
                <slot name="error" />
            </small>
        </div>

        <div
            v-else
            class="fade-in min-h-[1.5rem] break-words text-gray-900 dark:text-gray-200"
        >
            <slot name="view">
                <span class="italic text-gray-400">—</span>
            </slot>
        </div>
    </div>
</template>

<style scoped>
.fade-in {
    animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
