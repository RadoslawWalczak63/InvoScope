<script setup lang="ts">
import EditableField from '@/Components/EditableField.vue';
import {
    getInvoiceStatusSeverity,
    toDate,
    toDateString,
} from '@/Constants/Helpers';
import {
    Entity,
    Invoice,
    InvoiceItem,
    PaginatedResource,
    Resource,
} from '@/Constants/Interfaces';
import { Currency, InvoiceItemUnit, InvoiceStatus, InvoiceType } from '@/Enum';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InvoiceDownloadDialog from '@/Pages/Invoice/Partials/InvoiceDownloadDialog.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    Button,
    Card,
    Column,
    ConfirmDialog,
    DataTable,
    DatePicker,
    FloatLabel,
    InputNumber,
    InputText,
    Select,
    Tag,
    Textarea,
    useConfirm,
} from 'primevue';
import { computed, ref } from 'vue';

const props = defineProps<{
    invoice: Resource<Invoice>;
    entities: PaginatedResource<Entity>;
}>();

const confirm = useConfirm();
const isEditing = ref(false);
const isDownloadModalOpen = ref(false);

interface InvoiceForm {
    [key: string]: any;

    number: string;
    issue_date: Date | null;
    due_date: Date | null;
    paid_date: Date | null;
    type: string;
    status: string;
    currency: string;
    buyer_id: number;
    buyer: Entity;
    seller_id: number;
    seller: Entity;
    items: InvoiceItem[];
}

const form = useForm<InvoiceForm>({
    number: props.invoice.data.number,
    issue_date: toDate(props.invoice.data.issue_date),
    due_date: toDate(props.invoice.data.due_date),
    paid_date: toDate(props.invoice.data.paid_date),
    type: props.invoice.data.type,
    status: props.invoice.data.status,
    currency: props.invoice.data.currency,
    buyer_id: props.invoice.data.buyer_id,
    buyer: props.invoice.data.buyer,
    seller_id: props.invoice.data.seller_id,
    seller: props.invoice.data.seller,
    bank_account_number: props.invoice.data.bank_account_number,
    items: props.invoice.data.items.map((item) => ({
        ...item,
        quantity: Number(item.quantity),
        price: Number(item.price),
        tax_amount: Number(item.tax_amount),
        discount: Number(item.discount),
    })),
});

const invoiceTotals = computed(() => {
    let subtotal = 0;
    let tax = 0;
    let discount = 0;

    form.items.forEach((item) => {
        subtotal += item.price * item.quantity;
        tax += Number(item.tax_amount);
        discount += Number(item.discount);
    });

    return {
        subtotal,
        tax,
        discount,
        total: subtotal + tax - discount,
    };
});

const startEditing = () => {
    form.defaults({
        number: props.invoice.data.number,
        issue_date: toDate(props.invoice.data.issue_date),
        due_date: toDate(props.invoice.data.due_date),
        paid_date: toDate(props.invoice.data.paid_date),
        type: props.invoice.data.type,
        status: props.invoice.data.status,
        currency: props.invoice.data.currency,
        buyer_id: props.invoice.data.buyer_id,
        buyer: props.invoice.data.buyer,
        seller_id: props.invoice.data.seller_id,
        seller: props.invoice.data.seller,
        items: props.invoice.data.items.map((i) => ({
            ...i,
            quantity: Number(i.quantity),
            price: Number(i.price),
            tax_amount: Number(i.tax_amount),
            discount: Number(i.discount),
        })),
    });
    form.reset();
    isEditing.value = true;
};

const cancelEditing = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
};

const saveInvoice = () => {
    form.transform((data) => ({
        ...data,
        issue_date: toDateString(data.issue_date),
        due_date: toDateString(data.due_date),
        paid_date: toDateString(data.paid_date),
        buyer_id: data.buyer?.id,
        seller_id: data.seller?.id,
    })).put(route('invoices.update', props.invoice.data.id), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};

const deleteInvoice = () => {
    confirm.require({
        message:
            'Are you sure you want to delete this invoice? This cannot be undone.',
        header: 'Delete Invoice',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: { label: 'Cancel', severity: 'secondary', outlined: true },
        acceptProps: { label: 'Delete', severity: 'danger' },
        accept: () => {
            router.delete(route('invoices.destroy', props.invoice.data.id));
        },
    });
};

const openDownloadModal = () => {
    isDownloadModalOpen.value = true;
};

const addItem = () => {
    form.items.push({
        name: 'New Item',
        description: '',
        quantity: 1,
        price: 0,
        tax_amount: 0,
        discount: 0,
        unit: InvoiceItemUnit.PIECE,
    });
};

const removeItem = (index: number) => {
    form.items.splice(index, 1);
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: isEditing.value ? form.currency : props.invoice.data.currency,
    }).format(value);
};
</script>

<template>
    <Head :title="`Invoice ${invoice.data.number}`" />
    <ConfirmDialog />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col justify-between gap-4 md:flex-row md:items-center"
            >
                <div class="flex items-center gap-2">
                    <Link :href="route('invoices.index')">
                        <Button
                            icon="pi pi-arrow-left"
                            text
                            rounded
                            aria-label="Back"
                        />
                    </Link>

                    <div v-if="!isEditing" class="flex items-center gap-3">
                        <span
                            class="text-xl font-semibold text-gray-800 dark:text-white"
                        >
                            {{ invoice.data.number }}
                        </span>
                        <Tag
                            :value="invoice.data.type"
                            :severity="
                                invoice.data.type === 'Income'
                                    ? 'success'
                                    : 'warn'
                            "
                        />
                    </div>
                    <div v-else class="text-primary-600 text-xl font-semibold">
                        Editing {{ invoice.data.number }}
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
                            @click="deleteInvoice"
                        />
                        <Button
                            size="small"
                            label="Download"
                            icon="pi pi-download"
                            severity="success"
                            text
                            @click="openDownloadModal"
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
                            @click="saveInvoice"
                            :loading="form.processing"
                        />
                    </template>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <Card>
                    <template #title>Details</template>
                    <template #content>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <EditableField
                                label="Number"
                                :isEditing="isEditing"
                            >
                                <template #view
                                    >{{ invoice.data.number }}
                                </template>
                                <template #input>
                                    <InputText
                                        v-model="form.number"
                                        class="w-full"
                                    />
                                </template>
                            </EditableField>

                            <EditableField
                                label="Currency"
                                :isEditing="isEditing"
                            >
                                <template #view
                                    >{{ invoice.data.currency }}
                                </template>
                                <template #input>
                                    <Select
                                        v-model="form.currency"
                                        :options="Object.values(Currency)"
                                        class="w-full"
                                    />
                                </template>
                            </EditableField>

                            <EditableField
                                label="Status"
                                :isEditing="isEditing"
                            >
                                <template #view>
                                    <Tag
                                        :value="invoice.data.status"
                                        :severity="
                                            getInvoiceStatusSeverity(
                                                invoice.data.status,
                                            )
                                        "
                                    />
                                </template>
                                <template #input>
                                    <Select
                                        v-model="form.status"
                                        :options="Object.values(InvoiceStatus)"
                                        class="w-full"
                                    />
                                </template>
                            </EditableField>

                            <EditableField
                                label="Issue Date"
                                :isEditing="isEditing"
                            >
                                <template #view
                                    >{{ invoice.data.issue_date }}
                                </template>
                                <template #input>
                                    <DatePicker
                                        showIcon
                                        showClear
                                        fluid
                                        dateFormat="yy-mm-dd"
                                        iconDisplay="input"
                                        v-model="form.issue_date"
                                        class="w-full"
                                    />
                                </template>
                            </EditableField>

                            <EditableField
                                label="Due Date"
                                :isEditing="isEditing"
                            >
                                <template #view
                                    >{{ invoice.data.due_date }}
                                </template>
                                <template #input>
                                    <DatePicker
                                        showIcon
                                        showClear
                                        fluid
                                        iconDisplay="input"
                                        v-model="form.due_date"
                                        class="w-full"
                                        dateFormat="yy-mm-dd"
                                    />
                                </template>
                            </EditableField>

                            <EditableField
                                label="Paid Date"
                                :isEditing="isEditing"
                            >
                                <template #view
                                    >{{ invoice.data.paid_date || '—' }}
                                </template>
                                <template #input>
                                    <DatePicker
                                        v-model="form.paid_date"
                                        showIcon
                                        showClear
                                        fluid
                                        iconDisplay="input"
                                        dateFormat="yy-mm-dd"
                                    />
                                </template>
                            </EditableField>

                            <EditableField label="Type" :isEditing="isEditing">
                                <template #view
                                    >{{ invoice.data.type }}
                                </template>
                                <template #input>
                                    <Select
                                        v-model="form.type"
                                        :options="Object.values(InvoiceType)"
                                        class="w-full"
                                    />
                                </template>
                            </EditableField>

                            <div class="lg:col-span-2">
                                <EditableField
                                    label="Bank Account Number"
                                    :isEditing="isEditing"
                                >
                                    <template #view>
                                        {{ invoice.data.bank_account_number }}
                                    </template>

                                    <template #input>
                                        <InputText
                                            v-model="form.bank_account_number"
                                            class="w-full"
                                        />
                                    </template>
                                </EditableField>
                            </div>
                        </div>
                    </template>
                </Card>

                <Card>
                    <template #title>Parties</template>
                    <template #content>
                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                            <div
                                class="space-y-2 border-l-4 border-indigo-500 pl-4"
                            >
                                <div
                                    class="mb-1 text-xs uppercase tracking-wider text-gray-500"
                                >
                                    Bill From
                                </div>
                                <div>
                                    <div class="text-lg font-bold">
                                        <Select
                                            v-if="isEditing"
                                            v-model="form.buyer"
                                            dropdown
                                            :options="entities.data"
                                            optionLabel="name"
                                            size="small"
                                            filter
                                            class="mb-2"
                                        />
                                        <span v-else>{{
                                            invoice.data.buyer.name
                                        }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <div>
                                            {{
                                                isEditing
                                                    ? form.buyer?.address_line_1
                                                    : invoice.data.buyer
                                                          .address_line_1
                                            }}
                                        </div>
                                        <div>
                                            {{
                                                isEditing
                                                    ? form.buyer?.city
                                                    : invoice.data.buyer.city
                                            }},
                                            {{
                                                isEditing
                                                    ? form.buyer?.country
                                                    : invoice.data.buyer.country
                                            }}
                                        </div>
                                        <div class="mt-2 text-emerald-600">
                                            {{
                                                isEditing
                                                    ? form.buyer?.email
                                                    : invoice.data.buyer.email
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="space-y-2 border-l-4 border-emerald-500 pl-4"
                            >
                                <div
                                    class="mb-1 text-xs uppercase tracking-wider text-gray-500"
                                >
                                    Bill To
                                </div>
                                <div>
                                    <div class="text-lg font-bold">
                                        <Select
                                            v-if="isEditing"
                                            v-model="form.seller"
                                            dropdown
                                            :options="entities.data"
                                            optionLabel="name"
                                            size="small"
                                            filter
                                            class="mb-2"
                                        />
                                        <span v-else>{{
                                            invoice.data.seller.name
                                        }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <div>
                                            {{
                                                isEditing
                                                    ? form.seller
                                                          ?.address_line_1
                                                    : invoice.data.seller
                                                          .address_line_1
                                            }}
                                        </div>
                                        <div>
                                            {{
                                                isEditing
                                                    ? form.seller?.city
                                                    : invoice.data.seller.city
                                            }},
                                            {{
                                                isEditing
                                                    ? form.seller?.country
                                                    : invoice.data.seller
                                                          .country
                                            }}
                                        </div>
                                        <div class="mt-2 text-emerald-600">
                                            {{
                                                isEditing
                                                    ? form.seller?.email
                                                    : invoice.data.seller.email
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <Card>
                <template #title>
                    <div class="flex items-center justify-between">
                        <span>Items</span>
                        <Button
                            v-if="isEditing"
                            label="Add Line"
                            icon="pi pi-plus"
                            size="small"
                            outlined
                            @click="addItem"
                        />
                    </div>
                </template>
                <template #content>
                    <DataTable
                        :value="isEditing ? form.items : invoice.data.items"
                        stripedRows
                        class="p-datatable-sm"
                    >
                        <Column header="Description" style="width: 35%">
                            <template #body="slotProps">
                                <div v-if="!isEditing">
                                    <div class="font-medium">
                                        {{ slotProps.data.name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ slotProps.data.description }}
                                    </div>
                                    <div
                                        class="text-xs text-gray-400"
                                        v-if="slotProps.data.sku"
                                    >
                                        SKU: {{ slotProps.data.sku || '—' }}
                                    </div>
                                </div>
                                <div v-else class="my-2 flex flex-col gap-2">
                                    <FloatLabel variant="on">
                                        <InputText
                                            v-model="slotProps.data.name"
                                            id="item-name"
                                            class="w-full"
                                        />
                                        <label for="item-name">Name</label>
                                    </FloatLabel>
                                    <FloatLabel variant="on">
                                        <Textarea
                                            v-model="slotProps.data.description"
                                            rows="1"
                                            autoResize
                                            class="w-full text-sm"
                                            id="item-desc"
                                        />
                                        <label for="item-desc"
                                            >Description</label
                                        >
                                    </FloatLabel>
                                    <FloatLabel variant="on">
                                        <InputText
                                            v-model="slotProps.data.sku"
                                            id="item-sku"
                                            class="w-full"
                                        />
                                        <label for="item-sku">SKU</label>
                                    </FloatLabel>
                                </div>
                            </template>
                        </Column>

                        <Column header="Qty" style="width: 10%">
                            <template #body="slotProps">
                                <span v-if="!isEditing">
                                    {{ slotProps.data.quantity }}
                                    <span class="text-xs text-gray-500">{{
                                        slotProps.data.unit_abbreviation
                                    }}</span>
                                </span>
                                <div v-else>
                                    <InputNumber
                                        v-model="slotProps.data.quantity"
                                        showButtons
                                        buttonLayout="horizontal"
                                        :min="0"
                                        inputClass="w-16 text-center"
                                        class="w-full"
                                    />
                                    <FloatLabel variant="on" class="mt-4">
                                        <Select
                                            v-model="slotProps.data.unit"
                                            :options="
                                                Object.values(InvoiceItemUnit)
                                            "
                                            id="item-unit"
                                            class="w-full"
                                        />
                                        <label for="item-unit">Unit</label>
                                    </FloatLabel>
                                </div>
                            </template>
                        </Column>

                        <Column header="Price" style="width: 15%">
                            <template #body="slotProps">
                                <span v-if="!isEditing">{{
                                    formatCurrency(slotProps.data.price)
                                }}</span>
                                <InputNumber
                                    v-else
                                    v-model="slotProps.data.price"
                                    mode="currency"
                                    :currency="form.currency"
                                    locale="en-US"
                                    class="w-full"
                                />
                            </template>
                        </Column>

                        <Column header="Tax" style="width: 12%">
                            <template #body="slotProps">
                                <span v-if="!isEditing">{{
                                    formatCurrency(slotProps.data.tax_amount)
                                }}</span>
                                <InputNumber
                                    v-else
                                    v-model="slotProps.data.tax_amount"
                                    mode="currency"
                                    :currency="form.currency"
                                    locale="en-US"
                                    class="w-full"
                                />
                            </template>
                        </Column>

                        <Column
                            header="Total"
                            style="width: 15%"
                            alignFrozen="right"
                        >
                            <template #body="slotProps">
                                <div class="font-mono font-medium">
                                    {{
                                        formatCurrency(
                                            slotProps.data.price *
                                                slotProps.data.quantity +
                                                Number(
                                                    slotProps.data.tax_amount,
                                                ) -
                                                Number(slotProps.data.discount),
                                        )
                                    }}
                                </div>
                            </template>
                        </Column>

                        <Column v-if="isEditing" style="width: 5%">
                            <template #body="slotProps">
                                <Button
                                    icon="pi pi-trash"
                                    text
                                    severity="danger"
                                    @click="removeItem(slotProps.index)"
                                />
                            </template>
                        </Column>

                        <template #empty>
                            <div
                                class="flex flex-col items-center justify-center p-4"
                                v-if="!isEditing"
                            >
                                <Button
                                    label="Add Item"
                                    icon="pi pi-plus"
                                    size="small"
                                    class="ml-2"
                                    @click="
                                        () => {
                                            startEditing();
                                            addItem();
                                        }
                                    "
                                />
                            </div>
                        </template>
                    </DataTable>
                </template>

                <template #footer>
                    <div class="flex flex-col items-end gap-2 pt-4">
                        <div class="w-full space-y-2 md:w-1/3">
                            <div
                                class="flex justify-between text-sm text-gray-600"
                            >
                                <span>Subtotal</span>
                                <span>
                                    {{ formatCurrency(invoiceTotals.subtotal) }}
                                </span>
                            </div>
                            <div
                                class="flex justify-between text-sm text-gray-600"
                            >
                                <span>Tax</span>
                                <span>
                                    {{ formatCurrency(invoiceTotals.tax) }}
                                </span>
                            </div>
                            <div
                                class="flex justify-between border-b border-gray-200 pb-2 text-sm text-gray-600"
                            >
                                <span>Discount</span>
                                <span>
                                    -
                                    {{ formatCurrency(invoiceTotals.discount) }}
                                </span>
                            </div>
                            <div
                                class="flex justify-between text-xl font-bold text-gray-900 dark:text-white"
                            >
                                <span>Total</span>
                                <span>
                                    {{ formatCurrency(invoiceTotals.total) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <InvoiceDownloadDialog
            :open="isDownloadModalOpen"
            :invoice="invoice.data"
            @update:open="(newVal) => (isDownloadModalOpen = newVal)"
        />
    </AuthenticatedLayout>
</template>
