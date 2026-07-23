<?php

namespace App\Events;

use App\Models\JournalVoucher;
use Illuminate\Foundation\Events\Dispatchable;

class VoucherReversed
{
    use Dispatchable;

    public function __construct(
        public JournalVoucher $originalVoucher,
        public JournalVoucher $reversalVoucher
    ) {}
}
