<?php

namespace App\Contracts;

use App\Enums\VoucherType;
use DateTimeInterface;

interface VoucherNumberGenerator
{
    /**
     * Generate the next unique voucher number.
     */
    public function generate(VoucherType $type, ?DateTimeInterface $date = null): string;
}
