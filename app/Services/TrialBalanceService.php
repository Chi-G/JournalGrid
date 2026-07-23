<?php

namespace App\Services;

use App\Enums\VoucherStatus;
use App\Models\ChartOfAccount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TrialBalanceService
{
    /**
     * Generate computed trial balance rows for all postable accounts.
     *
     * @return Collection<int, array{
     *   account_id: int,
     *   code: string,
     *   name: string,
     *   type: string,
     *   normal_balance: string,
     *   debit_minor: int,
     *   credit_minor: int,
     *   net_debit_minor: int,
     *   net_credit_minor: int
     * }>
     */
    public function generate(): Collection
    {
        $accounts = ChartOfAccount::query()
            ->where('is_postable', true)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $postedAggregates = DB::table('journal_lines')
            ->join('journal_vouchers', 'journal_lines.journal_voucher_id', '=', 'journal_vouchers.id')
            ->where('journal_vouchers.status', '=', VoucherStatus::Posted->value)
            ->select(
                'journal_lines.account_id',
                DB::raw('SUM(journal_lines.debit_minor) as total_debit'),
                DB::raw('SUM(journal_lines.credit_minor) as total_credit')
            )
            ->groupBy('journal_lines.account_id')
            ->get()
            ->keyBy('account_id');

        return $accounts->map(function (ChartOfAccount $account) use ($postedAggregates) {
            $agg = $postedAggregates->get($account->id);
            $totalDebit = (int) ($agg->total_debit ?? 0);
            $totalCredit = (int) ($agg->total_credit ?? 0);

            $netDifference = $totalDebit - $totalCredit;

            $netDebit = 0;
            $netCredit = 0;

            if ($netDifference > 0) {
                $netDebit = $netDifference;
            } elseif ($netDifference < 0) {
                $netCredit = abs($netDifference);
            }

            return [
                'account_id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type->value,
                'normal_balance' => $account->normal_balance->value,
                'debit_minor' => $totalDebit,
                'credit_minor' => $totalCredit,
                'net_debit_minor' => $netDebit,
                'net_credit_minor' => $netCredit,
            ];
        });
    }
}
