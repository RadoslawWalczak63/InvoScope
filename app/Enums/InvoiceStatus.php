<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'Draft';
    case SENT = 'Sent';
    case PAID = 'Paid';
    case OVERDUE = 'Overdue';
}
