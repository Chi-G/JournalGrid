<?php

namespace App\Enums;

enum VoucherStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';
    case Reversed = 'reversed';
}
