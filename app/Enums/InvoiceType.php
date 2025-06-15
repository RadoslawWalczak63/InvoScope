<?php

namespace App\Enums;

enum InvoiceType: string
{
    case Income = 'Income';
    case Expense = 'Expense';
}
