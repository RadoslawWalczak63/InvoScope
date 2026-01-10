<script setup lang="ts">
import { Entity } from '@/Constants/Interfaces';
import { Currency, InvoiceStatus, InvoiceType } from '@/Enum';
import { useForm } from '@inertiajs/vue3';
import { Button, Dialog, FloatLabel, InputText, Select } from 'primevue';

defineProps<{
    open: boolean;
    entities: Entity[];
}>();

const emit = defineEmits(['update:open']);

interface CreateInvoiceErrors {
    number?: string;
    issue_date?: string;
    due_date?: string;
    type?: string;
    status?: string;
    buyer_id?: string;
    seller_id?: string;
}

interface CreateInvoiceForm {
    [key: string]: any;

    number: string;
    issue_date: Date | null;
    due_date: Date | null;
    type: string;
    status: string;
    buyer: Entity | null;
    seller: Entity | null;
    buyer_id: number | null;
    seller_id: number | null;
    currency: string;
}

const form = useForm<CreateInvoiceForm>({
    number: '',
    issue_date: new Date(),
    due_date: null,
    type: '',
    status: InvoiceStatus.DRAFT,
    buyer: null,
    seller: null,
    buyer_id: null,
    seller_id: null,
    currency: Currency.PLN,
}) as ReturnType<typeof useForm<CreateInvoiceForm>> & {
    errors: CreateInvoiceErrors;
};

const submit = () => {
    form.transform((data) => {
        return {
            ...data,
            buyer_id: data.buyer?.id,
            seller_id: data.seller?.id,
        };
    }).post(route('invoices.store'), {
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
        header="Create New Invoice"
        :style="{ width: '50rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        @update:visible="(val) => emit('update:open', val)"
    >
        <form @submit.prevent="submit" class="mt-4 flex flex-col gap-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            id="number"
                            v-model="form.number"
                            class="w-full"
                            :invalid="!!form.errors.number"
                        />
                        <label for="number">Invoice Number</label>
                    </FloatLabel>
                    <small class="text-red-500" v-if="form.errors.number">
                        {{ form.errors.number }}
                    </small>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <DatePicker
                            id="issue_date"
                            v-model="form.issue_date"
                            class="w-full"
                            :invalid="!!form.errors.issue_date"
                        />
                        <label for="issue_date">Issue Date</label>
                    </FloatLabel>
                    <small class="text-red-500" v-if="form.errors.issue_date">
                        {{ form.errors.issue_date }}
                    </small>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <DatePicker
                            id="due_date"
                            v-model="form.due_date"
                            class="w-full"
                            :invalid="!!form.errors.due_date"
                        />
                        <label for="due_date">Due Date</label>
                    </FloatLabel>
                    <small class="text-red-500" v-if="form.errors.due_date">
                        {{ form.errors.due_date }}
                    </small>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <Select
                            v-model="form.type"
                            inputId="type"
                            :options="Object.values(InvoiceType)"
                            class="w-full"
                            :invalid="!!form.errors.type"
                        />
                        <label for="type">Type</label>
                    </FloatLabel>
                    <small class="text-red-500" v-if="form.errors.type">
                        {{ form.errors.type }}
                    </small>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <Select
                            v-model="form.status"
                            inputId="status"
                            :options="Object.values(InvoiceStatus)"
                            class="w-full"
                            :invalid="!!form.errors.status"
                        />
                        <label for="status">Status</label>
                    </FloatLabel>
                    <small class="text-red-500" v-if="form.errors.status">
                        {{ form.errors.status }}
                    </small>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <Select
                            v-model="form.currency"
                            inputId="currency"
                            :options="Object.values(Currency)"
                            class="w-full"
                        />
                        <label for="currency">Currency</label>
                    </FloatLabel>

                    <small class="text-red-500" v-if="form.errors.currency">
                        {{ form.errors.currency }}
                    </small>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div class="space-y-2 border-l-4 border-indigo-500 pl-4">
                    <div
                        class="mb-1 text-xs uppercase tracking-wider text-gray-500"
                    >
                        Bill From
                    </div>
                    <div>
                        <div class="text-lg font-bold">
                            <Select
                                v-model="form.buyer"
                                dropdown
                                :options="entities"
                                optionLabel="name"
                                size="small"
                                filter
                                class="mb-2 w-full"
                                :invalid="!!form.errors.buyer_id"
                            />

                            <small
                                class="text-red-500"
                                v-if="form.errors.buyer_id"
                            >
                                {{ form.errors.buyer_id }}
                            </small>
                        </div>
                        <div class="text-sm text-gray-600">
                            <div>
                                {{ form.buyer?.address_line_1 }}
                            </div>
                            <div>
                                {{ form.buyer?.city
                                }}<span v-if="form.buyer">,</span>
                                {{ form.buyer?.country }}
                            </div>
                            <div class="mt-2 text-emerald-600">
                                {{ form.buyer?.email }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 border-l-4 border-emerald-500 pl-4">
                    <div
                        class="mb-1 text-xs uppercase tracking-wider text-gray-500"
                    >
                        Bill To
                    </div>
                    <div>
                        <div class="text-lg font-bold">
                            <Select
                                v-model="form.seller"
                                dropdown
                                :options="entities"
                                optionLabel="name"
                                size="small"
                                filter
                                class="mb-2 w-full"
                                :invalid="!!form.errors.seller_id"
                            />

                            <small
                                class="text-red-500"
                                v-if="form.errors.seller_id"
                            >
                                {{ form.errors.seller_id }}
                            </small>
                        </div>
                        <div class="text-sm text-gray-600">
                            <div>
                                {{ form.seller?.address_line_1 }}
                            </div>
                            <div>
                                {{ form.seller?.city
                                }}<span v-if="form.seller">,</span>
                                {{ form.seller?.country }}
                            </div>
                            <div class="mt-2 text-emerald-600">
                                {{ form.seller?.email }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-sm italic text-gray-500">
                Items can be added after creating the invoice header.
            </div>
        </form>

        <template #footer>
            <Button label="Cancel" icon="pi pi-times" text @click="close" />
            <Button
                label="Create & Edit"
                icon="pi pi-arrow-right"
                iconPos="right"
                @click="submit"
                :loading="form.processing"
            />
        </template>
    </Dialog>
</template>
