export interface Entity {
    id: number;
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
