<script setup lang="ts">
import { Invoice } from '@/Constants/Interfaces';
import { InvoiceTemplate } from '@/Enum';
import {
    Button,
    Dialog,
    Divider,
    ProgressSpinner,
    Select,
    Tag,
} from 'primevue';
import { computed, ref } from 'vue';

const props = defineProps<{
    open: boolean;
    invoice: Invoice;
}>();

const emit = defineEmits(['update:open']);

const isIframeLoaded = ref(false);
const selectedTemplate = ref(Object.values(InvoiceTemplate)[0]);

const previewUrl = computed(() => {
    return route('invoices.preview', {
        invoice: props.invoice.id,
        template: selectedTemplate.value,
    });
});

const originalFileUrl = computed(() => {
    return props.invoice.file_url || null;
});

const handleIframeLoad = () => {
    isIframeLoaded.value = true;
};

const handleDownloadGenerated = () => {
    const downloadUrl = route('invoices.preview', {
        invoice: props.invoice.id,
        template: selectedTemplate.value,
        download: true,
    });

    window.open(downloadUrl, '_blank');
};

const handleDownloadOriginal = () => {
    if (originalFileUrl.value) {
        window.open(originalFileUrl.value, '_blank');
    }
};

const close = () => {
    emit('update:open', false);
};
</script>

<template>
    <Dialog
        :visible="open"
        modal
        header="Download Invoice"
        :style="{ width: '75rem', maxWidth: '90vw' }"
        :breakpoints="{ '1199px': '85vw', '575px': '95vw' }"
        :draggable="false"
        class="p-0"
        @update:visible="(val) => emit('update:open', val)"
    >
        <div class="flex h-[70vh] flex-col lg:flex-row">
            <div
                class="border-surface-200 dark:border-surface-700 flex w-full flex-col gap-6 p-6 lg:w-1/3 lg:border-r"
            >
                <div v-if="originalFileUrl">
                    <div class="mb-3 flex items-center justify-between">
                        <span
                            class="text-surface-500 dark:text-surface-400 text-sm font-semibold"
                        >
                            Original Source
                        </span>

                        <Tag value="PDF" severity="secondary" class="text-xs" />
                    </div>

                    <div
                        class="border-surface-200 bg-surface-50 dark:border-surface-700 dark:bg-surface-800 flex flex-col gap-3 rounded-lg border p-4"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400 flex h-10 w-10 items-center justify-center rounded-full"
                            >
                                <i class="pi pi-file text-lg"></i>
                            </div>

                            <div class="flex flex-col overflow-hidden">
                                <span
                                    class="text-surface-900 dark:text-surface-0 truncate text-sm font-medium"
                                >
                                    Original Invoice
                                </span>

                                <span class="text-surface-500 text-xs">
                                    Uploaded by System
                                </span>
                            </div>
                        </div>

                        <Button
                            label="Download"
                            icon="pi pi-download"
                            severity="secondary"
                            outlined
                            size="small"
                            class="w-full"
                            @click="handleDownloadOriginal"
                        />
                    </div>
                </div>

                <Divider v-if="originalFileUrl" />

                <div class="flex flex-1 flex-col">
                    <div
                        class="text-surface-500 dark:text-surface-400 mb-2 text-sm font-semibold"
                    >
                        Generate PDF
                    </div>

                    <Select
                        v-model="selectedTemplate"
                        :options="Object.values(InvoiceTemplate)"
                    />
                </div>
            </div>

            <div
                class="bg-surface-100 dark:bg-surface-900 relative flex w-full flex-col lg:w-2/3"
            >
                <div
                    class="border-surface-200 bg-surface-0 dark:border-surface-700 dark:bg-surface-800 flex items-center justify-between border-b py-3 pl-4"
                >
                    <span
                        class="text-surface-700 dark:text-surface-200 font-medium"
                    >
                        <i class="pi pi-eye text-primary mr-2"></i>
                        Preview
                    </span>

                    <span class="text-surface-500 text-xs">
                        {{ selectedTemplate }}
                        Template
                    </span>
                </div>

                <div
                    class="bg-surface-200 dark:bg-surface-950 relative flex-1 overflow-hidden"
                >
                    <div
                        v-if="!isIframeLoaded"
                        class="text-surface-500 absolute inset-0 flex flex-col items-center justify-center"
                    >
                        <ProgressSpinner
                            style="width: 40px; height: 40px"
                            strokeWidth="4"
                        />

                        <span class="mt-2 text-sm">Rendering Preview...</span>
                    </div>

                    <iframe
                        :src="previewUrl"
                        class="h-full w-full border-none transition-opacity duration-300"
                        :class="{
                            'opacity-0': !isIframeLoaded,
                            'opacity-100': isIframeLoaded,
                        }"
                        @load="handleIframeLoad"
                    ></iframe>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <Button
                    label="Close"
                    severity="secondary"
                    text
                    @click="close"
                />

                <Button
                    label="Download"
                    icon="pi pi-print"
                    iconPos="right"
                    @click="handleDownloadGenerated"
                />
            </div>
        </template>
    </Dialog>
</template>
