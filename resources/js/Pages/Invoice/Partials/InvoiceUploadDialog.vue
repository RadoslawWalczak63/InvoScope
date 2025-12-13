<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import {
    Button,
    Dialog,
    FileUpload,
    FloatLabel,
    Message,
    Select,
} from 'primevue';
import { FileUploadSelectEvent } from 'primevue/fileupload';

defineProps<{
    open: boolean;
    models: Array<{ id: string; name: string }>;
}>();

const emit = defineEmits(['update:open']);

const form = useForm({
    files: [] as File[],
    model_id: '' as string,
});

const submit = () => {
    form.post(route('invoices.upload'), {
        onSuccess: () => {
            form.reset();
            emit('update:open', false);
        },
    });
};

const close = () => {
    form.reset();
    form.clearErrors();
    emit('update:open', false);
};

const onSelect = (event: FileUploadSelectEvent) => {
    form.files = event.files;
};

const formatSize = (bytes: number) => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};
</script>

<template>
    <Dialog
        :visible="open"
        modal
        header="Upload Invoice"
        :style="{ width: '60rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        @update:visible="(val) => emit('update:open', val)"
    >
        <form @submit.prevent="submit" class="flex flex-col gap-6 py-2">
            <FloatLabel variant="on">
                <Select
                    v-model="form.model_id"
                    :options="models"
                    optionLabel="name"
                    optionValue="id"
                    class="w-full"
                    inputId="model_id"
                    :invalid="!!form.errors.model_id"
                />
                <label for="model_id">Model</label>
            </FloatLabel>

            <small v-if="form.errors.model_id" class="text-red-500">
                {{ form.errors.model_id }}
            </small>

            <FileUpload
                name="files[]"
                multiple
                accept="image/*,application/pdf"
                class="w-full"
                @select="onSelect"
            >
                <template #header="{ chooseCallback, clearCallback, files }">
                    <div class="flex justify-end gap-3">
                        <Button
                            icon="pi pi-images"
                            label="Choose"
                            outlined
                            size="small"
                            @click="chooseCallback()"
                        />
                        <Button
                            icon="pi pi-times"
                            label="Clear"
                            outlined
                            severity="danger"
                            size="small"
                            :disabled="!files || files.length === 0"
                            @click="clearCallback()"
                        />
                    </div>
                </template>

                <template #content="{ files, removeFileCallback, messages }">
                    <div class="p-4">
                        <Message
                            v-for="message of messages"
                            :key="message"
                            severity="error"
                            class="mb-4"
                        >
                            {{ message }}
                        </Message>

                        <div
                            v-if="files.length"
                            class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:grid-cols-4"
                        >
                            <div
                                v-for="(file, index) in files"
                                :key="file.name + file.size"
                                class="group relative rounded-xl border bg-white p-4 shadow-sm"
                            >
                                <div
                                    class="mb-3 flex h-32 items-center justify-center rounded-lg bg-gray-100"
                                >
                                    <img
                                        v-if="file.type.startsWith('image/')"
                                        :src="file.objectURL"
                                        class="h-full w-full object-cover"
                                    />
                                    <i
                                        v-else
                                        class="pi pi-file-pdf text-4xl text-red-500"
                                    />
                                </div>

                                <div class="text-center">
                                    <div
                                        class="truncate text-sm font-medium"
                                        :title="file.name"
                                    >
                                        {{ file.name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ formatSize(file.size) }}
                                    </div>
                                </div>

                                <Button
                                    icon="pi pi-times"
                                    rounded
                                    severity="danger"
                                    size="small"
                                    class="absolute -right-2 -top-2 opacity-0 group-hover:opacity-100"
                                    @click="removeFileCallback(index)"
                                />
                            </div>
                        </div>
                    </div>
                </template>

                <template #empty>
                    <div
                        class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-12"
                    >
                        <i
                            class="pi pi-cloud-upload mb-4 text-4xl text-blue-500"
                        />
                        <p class="text-lg font-medium">
                            Drag and drop files here
                        </p>
                        <p class="text-sm text-gray-500">
                            Supports Images and PDFs
                        </p>
                    </div>
                </template>
            </FileUpload>
        </form>

        <template #footer>
            <Button label="Cancel" text icon="pi pi-times" @click="close" />
            <Button
                label="Upload"
                icon="pi pi-upload"
                :loading="form.processing"
                :disabled="form.files.length === 0 || !form.model_id"
                @click="submit"
            />
        </template>
    </Dialog>
</template>
