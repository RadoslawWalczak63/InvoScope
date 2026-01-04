import { InvoiceStatus, InvoiceType, QueuedJobStatus } from '@/Enum';

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
        default:
            return 'secondary';
    }
};

export const getQueuedJobSeverity = (status: string) => {
    switch (status) {
        case QueuedJobStatus.FINISHED:
            return 'success';
        case QueuedJobStatus.FAILED:
            return 'danger';
        case QueuedJobStatus.PROCESSING:
            return 'info';
        case QueuedJobStatus.NEW:
        case QueuedJobStatus.QUEUED:
            return 'warn';
        default:
            return 'secondary';
    }
};

export const toDate = (value?: string | null) =>
    value ? new Date(value + 'T00:00:00') : null;

export const toDateString = (value?: Date | null) => {
    if (!value) return null;

    const year = value.getFullYear();
    const month = String(value.getMonth() + 1).padStart(2, '0');
    const day = String(value.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};
