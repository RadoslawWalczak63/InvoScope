export interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
    updated_at: string;
}

export interface Entity {
    id: number;
    user_id: number;
    user?: User;
    type: string;
    name?: string;
    company_name?: string;
    first_name?: string;
    last_name?: string;
    email?: string;
    phone?: string;
    tax_id?: string;
    address_line_1?: string;
    address_line_2?: string;
    city?: string;
    postal_code?: string;
    state?: string;
    country?: string;
    created_at: string;
    updated_at: string;
}

export interface PaginationMeta {
    current_page: number;
    from: number | null;
    last_page: number;
    path: string;
    per_page: number;
    to: number | null;
    total: number;
    links?: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

export interface InvoiceItem {
    id?: number;
    name: string;
    description?: string;
    sku?: string;
    quantity: number;
    price: number;
    tax_amount: number;
    discount: number;
    unit: string;
    unit_abbreviation?: string;
    net_total?: number;
}

export interface Invoice {
    id: number;
    number: string;
    type: string;
    status: string;
    currency: string;
    issue_date: string;
    due_date: string;
    paid_date?: string;
    buyer_id: number;
    seller_id: number;
    bank_account_number: string;
    file_url?: string;
    buyer: Entity;
    seller: Entity;
    items: InvoiceItem[];
    created_at: string;
    updated_at: string;
}

export interface QueuedJob {
    [key: string]: any;

    id: number;
    user_id: number;
    user?: User;
    job: string;
    file_name: string;
    status: string;
    arguments: object;
    result: object;
    console_output: string;
    started_at: string;
    finished_at: string;
    created_at: string;
    updated_at: string;
}

export interface PaginatedResource<T> {
    data: T[];
    meta: PaginationMeta;
}

export interface Resource<T> {
    data: T;
}
