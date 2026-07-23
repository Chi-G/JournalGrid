<?php

namespace App\Events;

use App\Models\JournalVoucher;
use Illuminate\Foundation\Events\Dispatchable;

class VoucherPosted
{
    use Dispatchable;

    public function __construct(public JournalVoucher $voucher) {}
}
