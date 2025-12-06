<script setup lang="ts">
import EditableField from '@/Components/EditableField.vue';
import { Entity, Resource } from '@/Constants/Interfaces';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    Button,
    Card,
    ConfirmDialog,
    InputText,
    Tag,
    useConfirm,
} from 'primevue';
import { ref } from 'vue';

const props = defineProps<{
    entity: Resource<Entity>;
}>();

const confirm = useConfirm();
const isEditing = ref(false);

const form = useForm({
    company_name: props.entity.data.company_name,
    first_name: props.entity.data.first_name,
    last_name: props.entity.data.last_name,
    tax_id: props.entity.data.tax_id,
    email: props.entity.data.email,
    phone: props.entity.data.phone,
    address_line_1: props.entity.data.address_line_1,
    address_line_2: props.entity.data.address_line_2,
    city: props.entity.data.city,
    state: props.entity.data.state,
    postal_code: props.entity.data.postal_code,
    country: props.entity.data.country,
    type: props.entity.data.type,
});

const startEditing = () => {
    form.defaults({ ...props.entity.data });
    form.reset();
    isEditing.value = true;
};

const cancelEditing = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
};

const saveEntity = () => {
    form.put(route('entities.update', props.entity.data.id), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};

const deleteEntity = () => {
    confirm.require({
        message: 'Are you sure you want to delete this entity?',
        header: 'Danger Zone',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: { label: 'Cancel', severity: 'secondary', outlined: true },
        acceptProps: { label: 'Delete', severity: 'danger' },
        accept: () => {
            router.delete(route('entities.destroy', props.entity.data.id));
        },
    });
};
</script>

<template>
    <Head :title="entity.data.name" />
    <ConfirmDialog />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col justify-between gap-4 md:flex-row md:items-center"
            >
                <div class="flex items-center gap-2">
                    <Link :href="route('entities.index')">
                        <Button
                            icon="pi pi-arrow-left"
                            text
                            rounded
                            aria-label="Back"
                        />
                    </Link>

                    <div v-if="!isEditing" class="flex items-center gap-2">
                        <span
                            class="text-xl font-semibold text-gray-800 dark:text-white"
                        >
                            {{
                                entity.data.company_name ||
                                `${entity.data.first_name} ${entity.data.last_name}`
                            }}
                        </span>
                        <Tag :value="entity.data.type" class="uppercase" />
                    </div>
                    <div v-else class="text-primary-600 text-xl font-semibold">
                        Editing {{ entity.data.company_name || 'Entity' }}
                    </div>
                </div>

                <div class="flex gap-2">
                    <template v-if="!isEditing">
                        <Button
                            label="Edit"
                            icon="pi pi-pencil"
                            size="small"
                            text
                            @click="startEditing"
                        />
                        <Button
                            size="small"
                            label="Delete"
                            icon="pi pi-trash"
                            severity="danger"
                            text
                            @click="deleteEntity"
                        />
                    </template>

                    <template v-else>
                        <Button
                            label="Cancel"
                            icon="pi pi-times"
                            size="small"
                            severity="secondary"
                            text
                            @click="cancelEditing"
                            :disabled="form.processing"
                        />
                        <Button
                            label="Save Changes"
                            icon="pi pi-check"
                            size="small"
                            @click="saveEntity"
                            :loading="form.processing"
                        />
                    </template>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <Card>
                    <template #title>General Information</template>
                    <template #content>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <EditableField
                                label="Company Name"
                                :isEditing="isEditing"
                            >
                                <template #view>
                                    {{ entity.data.company_name ?? '—' }}
                                </template>
                                <template #input>
                                    <InputText
                                        v-model="form.company_name"
                                        class="w-full"
                                        placeholder="Company Name"
                                    />
                                </template>
                                <template #error>
                                    {{ form.errors.company_name }}
                                </template>
                            </EditableField>

                            <div class="grid grid-cols-2 gap-2">
                                <EditableField
                                    label="First Name"
                                    :isEditing="isEditing"
                                >
                                    <template #view>
                                        {{ entity.data.first_name }}
                                    </template>
                                    <template #input>
                                        <InputText
                                            v-model="form.first_name"
                                            class="w-full"
                                            placeholder="First"
                                        />
                                    </template>
                                </EditableField>
                                <EditableField
                                    label="Last Name"
                                    :isEditing="isEditing"
                                >
                                    <template #view>
                                        {{ entity.data.last_name }}
                                    </template>
                                    <template #input>
                                        <InputText
                                            v-model="form.last_name"
                                            class="w-full"
                                            placeholder="Last"
                                        />
                                    </template>
                                </EditableField>
                            </div>

                            <EditableField
                                label="Tax ID / VAT"
                                :isEditing="isEditing"
                            >
                                <template #view>
                                    <span
                                        class="inline-block rounded bg-gray-50 px-2 py-1 font-mono text-gray-700"
                                    >
                                        {{ entity.data.tax_id || '—' }}
                                    </span>
                                </template>
                                <template #input>
                                    <InputText
                                        v-model="form.tax_id"
                                        class="w-full font-mono"
                                    />
                                </template>
                            </EditableField>
                        </div>
                    </template>
                </Card>

                <Card>
                    <template #title>Address Details</template>
                    <template #content>
                        <div
                            class="grid grid-cols-1 gap-x-12 gap-y-6 md:grid-cols-2"
                        >
                            <EditableField
                                label="Street Address"
                                :isEditing="isEditing"
                                full-width
                            >
                                <template #view>
                                    <div>{{ entity.data.address_line_1 }}</div>
                                    <div v-if="entity.data.address_line_2">
                                        {{ entity.data.address_line_2 }}
                                    </div>
                                </template>
                                <template #input>
                                    <div class="space-y-2">
                                        <InputText
                                            v-model="form.address_line_1"
                                            class="w-full"
                                            placeholder="Line 1"
                                        />
                                        <InputText
                                            v-model="form.address_line_2"
                                            class="w-full"
                                            placeholder="Line 2 (Optional)"
                                        />
                                    </div>
                                </template>
                            </EditableField>

                            <EditableField label="City" :isEditing="isEditing">
                                <template #view
                                    >{{ entity.data.city }}
                                </template>
                                <template #input>
                                    <InputText
                                        v-model="form.city"
                                        class="w-full"
                                    />
                                </template>
                            </EditableField>

                            <EditableField
                                label="State / Province"
                                :isEditing="isEditing"
                            >
                                <template #view>
                                    {{ entity.data.state ?? '—' }}
                                </template>
                                <template #input>
                                    <InputText
                                        v-model="form.state"
                                        class="w-full"
                                    />
                                </template>
                            </EditableField>

                            <EditableField
                                label="Postal Code"
                                :isEditing="isEditing"
                            >
                                <template #view
                                    >{{ entity.data.postal_code }}
                                </template>
                                <template #input>
                                    <InputText
                                        v-model="form.postal_code"
                                        class="w-full"
                                    />
                                </template>
                            </EditableField>

                            <EditableField
                                label="Country"
                                :isEditing="isEditing"
                            >
                                <template #view>
                                    <div class="flex items-center gap-2">
                                        <i
                                            class="pi pi-globe text-gray-400"
                                        ></i>
                                        {{ entity.data.country }}
                                    </div>
                                </template>
                                <template #input>
                                    <InputText
                                        v-model="form.country"
                                        class="w-full"
                                    />
                                </template>
                            </EditableField>
                        </div>
                    </template>
                </Card>
            </div>

            <div class="space-y-6">
                <Card>
                    <template #title>
                        <div class="text-primary-700 dark:text-primary-300">
                            Contact
                        </div>
                    </template>
                    <template #content>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <div
                                    class="text-primary-600 mt-1 shrink-0 rounded-full bg-white p-2"
                                >
                                    <i class="pi pi-envelope"></i>
                                </div>
                                <div class="w-full overflow-hidden">
                                    <EditableField
                                        label="Email"
                                        :isEditing="isEditing"
                                    >
                                        <template #view>
                                            <a
                                                v-if="entity.data.email"
                                                :href="`mailto:${entity.data.email}`"
                                                class="text-primary-700 block truncate hover:underline"
                                            >
                                                {{ entity.data.email }}
                                            </a>
                                        </template>
                                        <template #input>
                                            <InputText
                                                v-model="form.email"
                                                class="p-inputtext-sm w-full"
                                                type="email"
                                            />
                                        </template>
                                        <template #error
                                            >{{ form.errors.email }}
                                        </template>
                                    </EditableField>
                                </div>
                            </li>

                            <li class="flex items-start gap-3">
                                <div
                                    class="text-primary-600 mt-1 shrink-0 rounded-full bg-white p-2"
                                >
                                    <i class="pi pi-phone"></i>
                                </div>
                                <div class="w-full">
                                    <EditableField
                                        label="Phone"
                                        :isEditing="isEditing"
                                    >
                                        <template #view>
                                            <a
                                                v-if="entity.data.phone"
                                                :href="`tel:${entity.data.phone}`"
                                                class="hover:text-primary-700 text-gray-900"
                                            >
                                                {{ entity.data.phone }}
                                            </a>
                                        </template>
                                        <template #input>
                                            <InputText
                                                v-model="form.phone"
                                                class="p-inputtext-sm w-full"
                                                placeholder="+1..."
                                            />
                                        </template>
                                    </EditableField>
                                </div>
                            </li>
                        </ul>
                    </template>
                </Card>

                <Card>
                    <template #title>System Data</template>
                    <template #content>
                        <div class="space-y-4 text-sm">
                            <div
                                class="flex justify-between border-b border-gray-100 py-2"
                            >
                                <span class="text-gray-500">Created At</span>
                                <span class="text-right">{{
                                    entity.data.created_at
                                }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-500">Last Updated</span>
                                <span class="text-right">{{
                                    entity.data.updated_at
                                }}</span>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
