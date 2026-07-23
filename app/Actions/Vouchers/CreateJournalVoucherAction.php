<?php

namespace App\Actions\Vouchers;

use App\Contracts\VoucherNumberGenerator;
use App\Enums\VoucherStatus;
use App\Enums\VoucherType;
use App\Models\JournalLine;
use App\Models\JournalVoucher;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreateJournalVoucherAction
{
    public function __construct(public VoucherNumberGenerator $numberGenerator) {}

    /**
     * Create a new draft journal voucher with lines.
     *
     * @param  array{
     *   voucher_date: string|\DateTimeInterface,
     *   type: VoucherType|string,
     *   narration?: string|null,
     *   lines: array<int, array{
     *     account_id: int,
     *     debit_minor: int,
     *     credit_minor: int,
     *     narration?: string|null,
     *   }>
     * }  $data
     */
    public function execute(array $data, User $creator): JournalVoucher
    {
        $linesData = array_values($data['lines'] ?? []);

        if (count($linesData) < 2) {
            throw new InvalidArgumentException('A journal voucher must contain at least two line items.');
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($linesData as $index => $line) {
            $debit = (int) ($line['debit_minor'] ?? 0);
            $credit = (int) ($line['credit_minor'] ?? 0);

            if ($debit > 0 && $credit > 0) {
                throw new InvalidArgumentException('Line #'.($index + 1).' cannot have both debit and credit amounts.');
            }

            if ($debit < 0 || $credit < 0) {
                throw new InvalidArgumentException('Line #'.($index + 1).' contains invalid negative minor amounts.');
            }

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        if ($totalDebit === 0 && $totalCredit === 0) {
            throw new InvalidArgumentException('Journal voucher lines cannot be zero total.');
        }

        if ($totalDebit !== $totalCredit) {
            throw new InvalidArgumentException("Journal voucher is not balanced: Total Debit ({$totalDebit}) does not equal Total Credit ({$totalCredit}).");
        }

        $type = $data['type'] instanceof VoucherType ? $data['type'] : VoucherType::from($data['type']);
        $voucherDate = Carbon::parse($data['voucher_date']);

        return DB::transaction(function () use ($data, $creator, $type, $voucherDate, $linesData, $totalDebit, $totalCredit) {
            $voucherNo = $this->numberGenerator->generate($type, $voucherDate);

            $voucher = JournalVoucher::create([
                'voucher_no' => $voucherNo,
                'voucher_date' => $voucherDate,
                'type' => $type,
                'narration' => $data['narration'] ?? null,
                'status' => VoucherStatus::Draft,
                'total_debit_minor' => $totalDebit,
                'total_credit_minor' => $totalCredit,
                'created_by' => $creator->id,
            ]);

            foreach ($linesData as $order => $line) {
                JournalLine::create([
                    'journal_voucher_id' => $voucher->id,
                    'account_id' => (int) $line['account_id'],
                    'debit_minor' => (int) ($line['debit_minor'] ?? 0),
                    'credit_minor' => (int) ($line['credit_minor'] ?? 0),
                    'narration' => $line['narration'] ?? null,
                    'line_order' => $order + 1,
                ]);
            }

            return $voucher->load('lines.account');
        });
    }
}
