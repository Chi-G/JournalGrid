<?php

namespace App\Enums;

enum VoucherType: string
{
    case General = 'general';
    case Cash = 'cash';
    case Bank = 'bank';
    case Adjustment = 'adjustment';
}
