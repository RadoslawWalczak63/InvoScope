<?php

namespace App\Enums;

enum InvoiceItemUnit: string
{
    case HOUR = 'Hour';
    case DAY = 'Day';
    case WEEK = 'Week';
    case MONTH = 'Month';
    case YEAR = 'Year';
    case PIECE = 'Piece';
    case SET = 'Set';
    case KIT = 'Kit';
    case BOX = 'Box';
    case PACK = 'Pack';
    case DOZEN = 'Dozen';
    case KILOGRAM = 'Kilogram';
    case GRAM = 'Gram';
    case TONNE = 'Tonne';
    case METER = 'Meter';
    case CENTIMETER = 'Centimeter';
    case SQUARE_METER = 'Square Meter';
    case LITER = 'Liter';

    case SERVICE = 'Service';
    case FLAT_RATE = 'Flat Rate';
    case PROJECT = 'Project';

    public function abbreviation(): string
    {
        return match ($this) {
            self::HOUR => 'h',
            self::DAY => 'd',
            self::WEEK => 'wk',
            self::MONTH => 'mo',
            self::YEAR => 'yr',
            self::PIECE => 'pcs',
            self::SET => 'set',
            self::KIT => 'kit',
            self::BOX => 'bx',
            self::PACK => 'pk',
            self::DOZEN => 'doz',
            self::KILOGRAM => 'kg',
            self::GRAM => 'g',
            self::TONNE => 't',
            self::METER => 'm',
            self::CENTIMETER => 'cm',
            self::SQUARE_METER => 'm²',
            self::LITER => 'l',
            self::SERVICE => 'svc',
            self::FLAT_RATE => 'flat',
            self::PROJECT => 'proj',
        };
    }
}
