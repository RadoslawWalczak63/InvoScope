import { InvoiceStatus, InvoiceType } from '@/Enum';

export const getInvoiceStatusSeverity = (status: string) => {
    switch (status) {
        case InvoiceStatus.PAID:
            return 'success';
        case InvoiceStatus.SENT:
            return 'info';
        case InvoiceStatus.OVERDUE:
            return 'danger';
        default:
            return 'secondary';
    }
};

export const getInvoiceTypeSeverity = (type: string) => {
    switch (type) {
        case InvoiceType.EXPENSE:
            return 'warn';
        case InvoiceType.INCOME:
            return 'success';
    }
};
