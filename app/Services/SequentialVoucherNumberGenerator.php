<?php

namespace App\Services;

use App\Contracts\VoucherNumberGenerator;
use App\Enums\VoucherType;
use App\Models\JournalVoucher;
use DateTimeInterface;
use Illuminate\Support\Carbon;

class SequentialVoucherNumberGenerator implements VoucherNumberGenerator
{
    public function generate(VoucherType $type, ?DateTimeInterface $date = null): string
    {
        $date = $date ? Carbon::instance($date) : Carbon::now();
        $prefix = match ($type) {
            VoucherType::Cash => 'CPV',
            VoucherType::Bank => 'BPV',
            VoucherType::Adjustment => 'ADJ',
            default => 'JV',
        };

        $yearMonth = $date->format('Ym');
        $prefixPattern = "{$prefix}-{$yearMonth}-";

        $latestVoucher = JournalVoucher::query()
            ->where('voucher_no', 'LIKE', "{$prefixPattern}%")
            ->orderByDesc('voucher_no')
            ->first();

        if (! $latestVoucher) {
            return "{$prefixPattern}0001";
        }

        $lastNumber = (int) substr($latestVoucher->voucher_no, -4);
        $nextNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);

        return "{$prefixPattern}{$nextNumber}";
    }
}
