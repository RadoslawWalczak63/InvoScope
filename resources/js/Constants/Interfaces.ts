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

export interface PaginatedResponse<T> {
    data: T[];
    meta: PaginationMeta;
}
