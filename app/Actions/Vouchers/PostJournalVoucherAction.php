<?php

namespace App\Actions\Vouchers;

use App\Enums\VoucherStatus;
use App\Events\VoucherPosted;
use App\Models\JournalVoucher;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PostJournalVoucherAction
{
    public function execute(JournalVoucher $voucher, User $poster): JournalVoucher
    {
        if ($voucher->status !== VoucherStatus::Draft) {
            throw new InvalidArgumentException("Only draft vouchers can be posted. Current status: {$voucher->status->value}");
        }

        if (! $poster->can('voucher.post')) {
            throw new AuthorizationException('User does not have permission to post vouchers.');
        }

        // Server-side balance re-validation
        $lineSumDebit = (int) $voucher->lines()->sum('debit_minor');
        $lineSumCredit = (int) $voucher->lines()->sum('credit_minor');

        if ($lineSumDebit !== $lineSumCredit || $lineSumDebit === 0) {
            throw new InvalidArgumentException("Voucher lines are not balanced (Debit: {$lineSumDebit}, Credit: {$lineSumCredit}).");
        }

        return DB::transaction(function () use ($voucher, $poster, $lineSumDebit, $lineSumCredit) {
            $voucher->update([
                'status' => VoucherStatus::Posted,
                'total_debit_minor' => $lineSumDebit,
                'total_credit_minor' => $lineSumCredit,
                'posted_by' => $poster->id,
                'posted_at' => Carbon::now(),
            ]);

            $voucher->refresh();

            VoucherPosted::dispatch($voucher);

            return $voucher;
        });
    }
}
