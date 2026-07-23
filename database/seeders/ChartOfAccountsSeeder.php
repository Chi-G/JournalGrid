<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\NormalBalance;
use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Group Headers (Non-postable)
            ['code' => '1000', 'name' => 'Assets', 'type' => AccountType::Asset, 'normal_balance' => NormalBalance::Debit, 'is_postable' => false],
            ['code' => '2000', 'name' => 'Liabilities', 'type' => AccountType::Liability, 'normal_balance' => NormalBalance::Credit, 'is_postable' => false],
            ['code' => '3000', 'name' => 'Equity', 'type' => AccountType::Equity, 'normal_balance' => NormalBalance::Credit, 'is_postable' => false],
            ['code' => '4000', 'name' => 'Income', 'type' => AccountType::Income, 'normal_balance' => NormalBalance::Credit, 'is_postable' => false],
            ['code' => '5000', 'name' => 'Expenses', 'type' => AccountType::Expense, 'normal_balance' => NormalBalance::Debit, 'is_postable' => false],
        ];

        foreach ($accounts as $acc) {
            ChartOfAccount::firstOrCreate(['code' => $acc['code']], $acc);
        }

        $parentAssets = ChartOfAccount::where('code', '1000')->first()->id;
        $parentLiabilities = ChartOfAccount::where('code', '2000')->first()->id;
        $parentEquity = ChartOfAccount::where('code', '3000')->first()->id;
        $parentIncome = ChartOfAccount::where('code', '4000')->first()->id;
        $parentExpenses = ChartOfAccount::where('code', '5000')->first()->id;

        $postableAccounts = [
            // Assets
            ['code' => '1010', 'name' => 'Cash on Hand', 'type' => AccountType::Asset, 'normal_balance' => NormalBalance::Debit, 'parent_id' => $parentAssets, 'is_postable' => true],
            ['code' => '1020', 'name' => 'Operating Bank Account', 'type' => AccountType::Asset, 'normal_balance' => NormalBalance::Debit, 'parent_id' => $parentAssets, 'is_postable' => true],
            ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => AccountType::Asset, 'normal_balance' => NormalBalance::Debit, 'parent_id' => $parentAssets, 'is_postable' => true],

            // Liabilities
            ['code' => '2010', 'name' => 'Accounts Payable', 'type' => AccountType::Liability, 'normal_balance' => NormalBalance::Credit, 'parent_id' => $parentLiabilities, 'is_postable' => true],
            ['code' => '2020', 'name' => 'Accrued Payroll', 'type' => AccountType::Liability, 'normal_balance' => NormalBalance::Credit, 'parent_id' => $parentLiabilities, 'is_postable' => true],

            // Equity
            ['code' => '3010', 'name' => 'Owner Capital', 'type' => AccountType::Equity, 'normal_balance' => NormalBalance::Credit, 'parent_id' => $parentEquity, 'is_postable' => true],
            ['code' => '3020', 'name' => 'Retained Earnings', 'type' => AccountType::Equity, 'normal_balance' => NormalBalance::Credit, 'parent_id' => $parentEquity, 'is_postable' => true],

            // Income
            ['code' => '4010', 'name' => 'Sales Revenue', 'type' => AccountType::Income, 'normal_balance' => NormalBalance::Credit, 'parent_id' => $parentIncome, 'is_postable' => true],
            ['code' => '4020', 'name' => 'Service Income', 'type' => AccountType::Income, 'normal_balance' => NormalBalance::Credit, 'parent_id' => $parentIncome, 'is_postable' => true],

            // Expenses
            ['code' => '5010', 'name' => 'Rent Expense', 'type' => AccountType::Expense, 'normal_balance' => NormalBalance::Debit, 'parent_id' => $parentExpenses, 'is_postable' => true],
            ['code' => '5020', 'name' => 'Salaries & Wages', 'type' => AccountType::Expense, 'normal_balance' => NormalBalance::Debit, 'parent_id' => $parentExpenses, 'is_postable' => true],
            ['code' => '5030', 'name' => 'Office Supplies Expense', 'type' => AccountType::Expense, 'normal_balance' => NormalBalance::Debit, 'parent_id' => $parentExpenses, 'is_postable' => true],
        ];

        foreach ($postableAccounts as $acc) {
            ChartOfAccount::firstOrCreate(['code' => $acc['code']], $acc);
        }
    }
}
