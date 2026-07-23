<?php

namespace Tests\Unit;

use App\Actions\Vouchers\CreateJournalVoucherAction;
use App\Enums\AccountType;
use App\Enums\NormalBalance;
use App\Enums\VoucherStatus;
use App\Enums\VoucherType;
use App\Models\ChartOfAccount;
use App\Models\User;
use App\Services\SequentialVoucherNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class CreateJournalVoucherActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_draft_journal_voucher_when_balanced(): void
    {
        $user = User::factory()->create();
        $account1 = ChartOfAccount::create(['code' => '1010', 'name' => 'Cash', 'type' => AccountType::Asset, 'normal_balance' => NormalBalance::Debit]);
        $account2 = ChartOfAccount::create(['code' => '4010', 'name' => 'Sales', 'type' => AccountType::Income, 'normal_balance' => NormalBalance::Credit]);

        $action = new CreateJournalVoucherAction(new SequentialVoucherNumberGenerator);

        $voucher = $action->execute([
            'voucher_date' => '2026-07-23',
            'type' => VoucherType::General,
            'narration' => 'Test Sales Journal',
            'lines' => [
                ['account_id' => $account1->id, 'debit_minor' => 500000, 'credit_minor' => 0, 'narration' => 'Cash received'],
                ['account_id' => $account2->id, 'debit_minor' => 0, 'credit_minor' => 500000, 'narration' => 'Revenue recognized'],
            ],
        ], $user);

        $this->assertEquals(VoucherStatus::Draft, $voucher->status);
        $this->assertEquals(500000, $voucher->total_debit_minor);
        $this->assertEquals(500000, $voucher->total_credit_minor);
        $this->assertEquals(2, $voucher->lines()->count());
    }

    public function test_throws_exception_when_unbalanced(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user = User::factory()->create();
        $account1 = ChartOfAccount::create(['code' => '1010', 'name' => 'Cash', 'type' => AccountType::Asset, 'normal_balance' => NormalBalance::Debit]);
        $account2 = ChartOfAccount::create(['code' => '4010', 'name' => 'Sales', 'type' => AccountType::Income, 'normal_balance' => NormalBalance::Credit]);

        $action = new CreateJournalVoucherAction(new SequentialVoucherNumberGenerator);

        $action->execute([
            'voucher_date' => '2026-07-23',
            'type' => VoucherType::General,
            'lines' => [
                ['account_id' => $account1->id, 'debit_minor' => 500000, 'credit_minor' => 0],
                ['account_id' => $account2->id, 'debit_minor' => 0, 'credit_minor' => 300000],
            ],
        ], $user);
    }
}
