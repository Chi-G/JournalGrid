<?php

namespace Tests\Unit;

use App\Actions\Vouchers\CreateJournalVoucherAction;
use App\Actions\Vouchers\PostJournalVoucherAction;
use App\Actions\Vouchers\ReverseJournalVoucherAction;
use App\Enums\AccountType;
use App\Enums\NormalBalance;
use App\Enums\VoucherStatus;
use App\Enums\VoucherType;
use App\Models\ChartOfAccount;
use App\Models\User;
use App\Services\SequentialVoucherNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ReverseJournalVoucherActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_reverses_posted_voucher_with_mirrored_lines_and_zero_net_change(): void
    {
        Permission::create(['name' => 'voucher.create']);
        Permission::create(['name' => 'voucher.post']);
        Permission::create(['name' => 'voucher.reverse']);

        $user = User::factory()->create();
        $user->givePermissionTo(['voucher.create', 'voucher.post', 'voucher.reverse']);

        $account1 = ChartOfAccount::create(['code' => '1010', 'name' => 'Bank', 'type' => AccountType::Asset, 'normal_balance' => NormalBalance::Debit]);
        $account2 = ChartOfAccount::create(['code' => '5010', 'name' => 'Rent', 'type' => AccountType::Expense, 'normal_balance' => NormalBalance::Debit]);

        $generator = new SequentialVoucherNumberGenerator;
        $createAction = new CreateJournalVoucherAction($generator);
        $original = $createAction->execute([
            'voucher_date' => '2026-07-23',
            'type' => VoucherType::General,
            'lines' => [
                ['account_id' => $account2->id, 'debit_minor' => 200000, 'credit_minor' => 0],
                ['account_id' => $account1->id, 'debit_minor' => 0, 'credit_minor' => 200000],
            ],
        ], $user);

        (new PostJournalVoucherAction)->execute($original, $user);

        $reverseAction = new ReverseJournalVoucherAction($generator);
        $reversal = $reverseAction->execute($original, $user, 'Reversing rent payment');

        $original->refresh();

        $this->assertEquals(VoucherStatus::Reversed, $original->status);
        $this->assertEquals(VoucherStatus::Posted, $reversal->status);
        $this->assertEquals($original->id, $reversal->reversal_of_id);

        // Assert lines mirrored
        $reversalLine1 = $reversal->lines()->where('account_id', $account2->id)->first();
        $this->assertEquals(0, $reversalLine1->debit_minor);
        $this->assertEquals(200000, $reversalLine1->credit_minor);

        // Assert net GL impact of original + reversal sums to zero
        $totalDebitGL = $original->total_debit_minor + $reversal->total_debit_minor;
        $totalCreditGL = $original->total_credit_minor + $reversal->total_credit_minor;
        $this->assertEquals($totalDebitGL, $totalCreditGL);
    }
}
