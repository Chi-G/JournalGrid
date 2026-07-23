<?php

namespace Database\Seeders;

use App\Actions\Vouchers\CreateJournalVoucherAction;
use App\Actions\Vouchers\PostJournalVoucherAction;
use App\Enums\VoucherType;
use App\Models\ChartOfAccount;
use App\Models\User;
use App\Services\SequentialVoucherNumberGenerator;
use Illuminate\Database\Seeder;

class JournalVoucherSeeder extends Seeder
{
    public function run(): void
    {
        $accountant = User::where('email', 'accountant@journalgrid.com')->first();
        $approver = User::where('email', 'approver@journalgrid.com')->first();

        if (! $accountant || ! $approver) {
            return;
        }

        $cash = ChartOfAccount::where('code', '1010')->first();
        $bank = ChartOfAccount::where('code', '1020')->first();
        $ar = ChartOfAccount::where('code', '1200')->first();
        $ap = ChartOfAccount::where('code', '2010')->first();
        $accruedPayroll = ChartOfAccount::where('code', '2020')->first();
        $capital = ChartOfAccount::where('code', '3010')->first();
        $sales = ChartOfAccount::where('code', '4010')->first();
        $serviceIncome = ChartOfAccount::where('code', '4020')->first();
        $rent = ChartOfAccount::where('code', '5010')->first();
        $salaries = ChartOfAccount::where('code', '5020')->first();
        $officeSupplies = ChartOfAccount::where('code', '5030')->first();

        $createAction = new CreateJournalVoucherAction(new SequentialVoucherNumberGenerator);
        $postAction = new PostJournalVoucherAction;

        $vouchersToCreate = [
            // 1. Initial Capital Investment
            [
                'date' => now()->subDays(60)->toDateString(),
                'type' => VoucherType::General,
                'narration' => 'Initial owner equity injection into company bank account',
                'lines' => [
                    ['account_id' => $bank->id, 'debit_minor' => 1000000000, 'credit_minor' => 0, 'narration' => 'Bank capital deposit'],
                    ['account_id' => $capital->id, 'debit_minor' => 0, 'credit_minor' => 1000000000, 'narration' => 'Owner capital share'],
                ],
                'post' => true,
            ],
            // 2. Office Space Annual Lease Payment
            [
                'date' => now()->subDays(55)->toDateString(),
                'type' => VoucherType::Bank,
                'narration' => 'Annual office rental lease payment',
                'lines' => [
                    ['account_id' => $rent->id, 'debit_minor' => 120000000, 'credit_minor' => 0, 'narration' => 'Prepaid office rent'],
                    ['account_id' => $bank->id, 'debit_minor' => 0, 'credit_minor' => 120000000, 'narration' => 'Bank transfer to landlord'],
                ],
                'post' => true,
            ],
            // 3. Purchase Office Supplies on Credit
            [
                'date' => now()->subDays(45)->toDateString(),
                'type' => VoucherType::General,
                'narration' => 'Bulk stationery & printing paper from PaperCo',
                'lines' => [
                    ['account_id' => $officeSupplies->id, 'debit_minor' => 15000000, 'credit_minor' => 0, 'narration' => 'Stationery stock'],
                    ['account_id' => $ap->id, 'debit_minor' => 0, 'credit_minor' => 15000000, 'narration' => 'Invoice #P-882'],
                ],
                'post' => true,
            ],
            // 4. Software Development Services Invoice
            [
                'date' => now()->subDays(40)->toDateString(),
                'type' => VoucherType::General,
                'narration' => 'Enterprise app dev milestone 1 invoice to Acme Corp',
                'lines' => [
                    ['account_id' => $ar->id, 'debit_minor' => 350000000, 'credit_minor' => 0, 'narration' => 'Receivable Acme Corp'],
                    ['account_id' => $serviceIncome->id, 'debit_minor' => 0, 'credit_minor' => 350000000, 'narration' => 'Software services revenue'],
                ],
                'post' => true,
            ],
            // 5. Payment Received from Client Acme Corp
            [
                'date' => now()->subDays(30)->toDateString(),
                'type' => VoucherType::Cash,
                'narration' => 'Acme Corp settlement of milestone 1 invoice',
                'lines' => [
                    ['account_id' => $bank->id, 'debit_minor' => 350000000, 'credit_minor' => 0, 'narration' => 'Direct wire transfer'],
                    ['account_id' => $ar->id, 'debit_minor' => 0, 'credit_minor' => 350000000, 'narration' => 'Clear receivables Acme'],
                ],
                'post' => true,
            ],
            // 6. Direct Counter Cash Sales
            [
                'date' => now()->subDays(25)->toDateString(),
                'type' => VoucherType::Cash,
                'narration' => 'Over-the-counter POS retail product sales',
                'lines' => [
                    ['account_id' => $cash->id, 'debit_minor' => 85000000, 'credit_minor' => 0, 'narration' => 'Cash received at till'],
                    ['account_id' => $sales->id, 'debit_minor' => 0, 'credit_minor' => 85000000, 'narration' => 'Direct product sales revenue'],
                ],
                'post' => true,
            ],
            // 7. Settlement of Supplier Invoice PaperCo
            [
                'date' => now()->subDays(20)->toDateString(),
                'type' => VoucherType::Bank,
                'narration' => 'Full settlement of PaperCo invoice #P-882',
                'lines' => [
                    ['account_id' => $ap->id, 'debit_minor' => 15000000, 'credit_minor' => 0, 'narration' => 'Debit accounts payable'],
                    ['account_id' => $bank->id, 'debit_minor' => 0, 'credit_minor' => 15000000, 'narration' => 'Outward bank transfer'],
                ],
                'post' => true,
            ],
            // 8. Month-End Payroll Accrual
            [
                'date' => now()->subDays(15)->toDateString(),
                'type' => VoucherType::Adjustment,
                'narration' => 'Monthly staff salaries provision for current period',
                'lines' => [
                    ['account_id' => $salaries->id, 'debit_minor' => 240000000, 'credit_minor' => 0, 'narration' => 'Salaries expense allocation'],
                    ['account_id' => $accruedPayroll->id, 'debit_minor' => 0, 'credit_minor' => 240000000, 'narration' => 'Accrued payroll liability'],
                ],
                'post' => true,
            ],
            // 9. Payment of Payroll via Bank
            [
                'date' => now()->subDays(10)->toDateString(),
                'type' => VoucherType::Bank,
                'narration' => 'Disbursement of staff salaries via bank transfer',
                'lines' => [
                    ['account_id' => $accruedPayroll->id, 'debit_minor' => 240000000, 'credit_minor' => 0, 'narration' => 'Clear payroll liability'],
                    ['account_id' => $bank->id, 'debit_minor' => 0, 'credit_minor' => 240000000, 'narration' => 'Bank payroll batch transfer'],
                ],
                'post' => true,
            ],
            // 10. IT Infrastructure Consulting Invoice (Draft)
            [
                'date' => now()->subDays(5)->toDateString(),
                'type' => VoucherType::General,
                'narration' => 'Cloud architecture setup billing to Nexus Ltd',
                'lines' => [
                    ['account_id' => $ar->id, 'debit_minor' => 180000000, 'credit_minor' => 0, 'narration' => 'Receivable Nexus Ltd'],
                    ['account_id' => $serviceIncome->id, 'debit_minor' => 0, 'credit_minor' => 180000000, 'narration' => 'Consulting services revenue'],
                ],
                'post' => false,
            ],
            // 11. Mid-Month Office Stationery Replenishment (Draft)
            [
                'date' => now()->subDays(2)->toDateString(),
                'type' => VoucherType::Cash,
                'narration' => 'Petty cash purchase of printer cartridges',
                'lines' => [
                    ['account_id' => $officeSupplies->id, 'debit_minor' => 4500000, 'credit_minor' => 0, 'narration' => 'Printer ink cartridges'],
                    ['account_id' => $cash->id, 'debit_minor' => 0, 'credit_minor' => 4500000, 'narration' => 'Petty cash disbursement'],
                ],
                'post' => false,
            ],
            // 12. Upcoming Salaries Allocation (Draft)
            [
                'date' => now()->toDateString(),
                'type' => VoucherType::Adjustment,
                'narration' => 'Upcoming end-of-month payroll allocation (Awaiting Approval)',
                'lines' => [
                    ['account_id' => $salaries->id, 'debit_minor' => 250000000, 'credit_minor' => 0, 'narration' => 'Gross payroll estimate'],
                    ['account_id' => $accruedPayroll->id, 'debit_minor' => 0, 'credit_minor' => 250000000, 'narration' => 'Accrued salaries liability'],
                ],
                'post' => false,
            ],
        ];

        foreach ($vouchersToCreate as $vData) {
            $v = $createAction->execute([
                'voucher_date' => $vData['date'],
                'type' => $vData['type'],
                'narration' => $vData['narration'],
                'lines' => $vData['lines'],
            ], $accountant);

            if ($vData['post']) {
                $postAction->execute($v, $approver);
            }
        }
    }
}
