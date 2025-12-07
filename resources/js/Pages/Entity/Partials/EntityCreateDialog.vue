<script setup lang="ts">
import { EntityType } from '@/Enum';
import { useForm } from '@inertiajs/vue3';
import { Button, Dialog, FloatLabel, InputText, Select } from 'primevue';

defineProps<{
    open: boolean;
}>();

const emit = defineEmits(['update:open', 'close']);

const form = useForm({
    type: '',
    company_name: '',
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    tax_id: '',
    address_line_1: '',
    city: '',
    country: '',
    postal_code: '',
});

const submit = () => {
    form.post(route('entities.store'), {
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
</script>

<template>
    <Dialog
        :visible="open"
        modal
        header="Create New Entity"
        :style="{ width: '50rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        @update:visible="(val) => emit('update:open', val)"
    >
        <form @submit.prevent="submit" class="mt-4 flex flex-col gap-6">
            <div class="grid grid-cols-1">
                <FloatLabel variant="on">
                    <Select
                        v-model="form.type"
                        inputId="create-type"
                        :options="Object.values(EntityType)"
                        class="w-full"
                        :invalid="!!form.errors.type"
                    />
                    <label for="create-type">Entity Type *</label>
                </FloatLabel>
                <small class="text-red-500" v-if="form.errors.type">{{
                    form.errors.type
                }}</small>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <FloatLabel variant="on">
                        <InputText
                            id="company_name"
                            v-model="form.company_name"
                            class="w-full"
                            :invalid="!!form.errors.company_name"
                        />
                        <label for="company_name">Company Name</label>
                    </FloatLabel>
                    <small
                        class="text-red-500"
                        v-if="form.errors.company_name"
                        >{{ form.errors.company_name }}</small
                    >
                </div>

                <div>
                    <FloatLabel variant="on">
                        <InputText
                            id="first_name"
                            v-model="form.first_name"
                            class="w-full"
                            :invalid="!!form.errors.first_name"
                        />
                        <label for="first_name">First Name</label>
                    </FloatLabel>
                    <small class="text-red-500" v-if="form.errors.first_name">{{
                        form.errors.first_name
                    }}</small>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <InputText
                            id="last_name"
                            v-model="form.last_name"
                            class="w-full"
                            :invalid="!!form.errors.last_name"
                        />
                        <label for="last_name">Last Name</label>
                    </FloatLabel>
                    <small class="text-red-500" v-if="form.errors.last_name">{{
                        form.errors.last_name
                    }}</small>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            id="email"
                            v-model="form.email"
                            class="w-full"
                            :invalid="!!form.errors.email"
                        />
                        <label for="email">Email</label>
                    </FloatLabel>
                    <small class="text-red-500" v-if="form.errors.email">{{
                        form.errors.email
                    }}</small>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <InputText
                            id="phone"
                            v-model="form.phone"
                            class="w-full"
                            :invalid="!!form.errors.phone"
                        />
                        <label for="phone">Phone</label>
                    </FloatLabel>
                    <small class="text-red-500" v-if="form.errors.phone">{{
                        form.errors.phone
                    }}</small>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <FloatLabel variant="on">
                        <InputText
                            id="address"
                            v-model="form.address_line_1"
                            class="w-full"
                        />
                        <label for="address">Address</label>
                    </FloatLabel>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <InputText
                            id="city"
                            v-model="form.city"
                            class="w-full"
                        />
                        <label for="city">City</label>
                    </FloatLabel>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <InputText
                            id="postal_code"
                            v-model="form.postal_code"
                            class="w-full"
                        />
                        <label for="postal_code">Postal Code</label>
                    </FloatLabel>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <InputText
                            id="country"
                            v-model="form.country"
                            class="w-full"
                        />
                        <label for="country">Country</label>
                    </FloatLabel>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <InputText
                            id="tax_id"
                            v-model="form.tax_id"
                            class="w-full"
                        />
                        <label for="tax_id">Tax ID</label>
                    </FloatLabel>
                </div>
            </div>
        </form>

        <template #footer>
            <Button label="Cancel" icon="pi pi-times" text @click="close" />
            <Button
                label="Save"
                icon="pi pi-check"
                @click="submit"
                :loading="form.processing"
            />
        </template>
    </Dialog>
</template>
