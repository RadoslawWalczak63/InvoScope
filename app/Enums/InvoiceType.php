<?php

namespace App\Enums;

enum InvoiceType: string
{
    case INCOME = 'Income';
    case EXPENSE = 'Expense';
}
