<?php

namespace App\Actions\Vouchers;

use App\Contracts\VoucherNumberGenerator;
use App\Enums\VoucherStatus;
use App\Events\VoucherReversed;
use App\Models\JournalLine;
use App\Models\JournalVoucher;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReverseJournalVoucherAction
{
    public function __construct(public VoucherNumberGenerator $numberGenerator) {}

    public function execute(JournalVoucher $originalVoucher, User $user, ?string $narration = null): JournalVoucher
    {
        if ($originalVoucher->status !== VoucherStatus::Posted) {
            throw new InvalidArgumentException("Only posted vouchers can be reversed. Current status: {$originalVoucher->status->value}");
        }

        if (! $user->can('voucher.reverse')) {
            throw new AuthorizationException('User does not have permission to reverse vouchers.');
        }

        return DB::transaction(function () use ($originalVoucher, $user, $narration) {
            $now = Carbon::now();
            $reversalNo = $this->numberGenerator->generate($originalVoucher->type, $now);

            $reversalVoucher = JournalVoucher::create([
                'voucher_no' => $reversalNo,
                'voucher_date' => $now->toDateString(),
                'type' => $originalVoucher->type,
                'narration' => $narration ?? "Reversal of Voucher #{$originalVoucher->voucher_no}",
                'status' => VoucherStatus::Posted,
                'total_debit_minor' => $originalVoucher->total_credit_minor,
                'total_credit_minor' => $originalVoucher->total_debit_minor,
                'created_by' => $user->id,
                'posted_by' => $user->id,
                'posted_at' => $now,
                'reversal_of_id' => $originalVoucher->id,
            ]);

            foreach ($originalVoucher->lines as $line) {
                JournalLine::create([
                    'journal_voucher_id' => $reversalVoucher->id,
                    'account_id' => $line->account_id,
                    'debit_minor' => $line->credit_minor, // mirrored
                    'credit_minor' => $line->debit_minor, // mirrored
                    'narration' => "Reversal: {$line->narration}",
                    'line_order' => $line->line_order,
                ]);
            }

            $originalVoucher->update([
                'status' => VoucherStatus::Reversed,
            ]);

            VoucherReversed::dispatch($originalVoucher, $reversalVoucher);

            return $reversalVoucher->load('lines.account');
        });
    }
}
