<?php

namespace App\Enums;

enum InvoiceTemplate: string
{
    case STANDARD = 'Standard';
    case MODERN = 'Modern';
    case MINIMALIST = 'Minimalist';
    case CLEAN = 'Clean';
    case PROFESSIONAL = 'Professional';
    case ARCHITECTURAL = 'Architectural';
    case HORIZONTAL = 'Horizontal';
}
