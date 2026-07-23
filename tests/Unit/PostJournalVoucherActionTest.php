<?php

namespace Tests\Unit;

use App\Actions\Vouchers\CreateJournalVoucherAction;
use App\Actions\Vouchers\PostJournalVoucherAction;
use App\Enums\AccountType;
use App\Enums\NormalBalance;
use App\Enums\VoucherStatus;
use App\Enums\VoucherType;
use App\Models\ChartOfAccount;
use App\Models\User;
use App\Services\SequentialVoucherNumberGenerator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PostJournalVoucherActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_approver_can_post_balanced_draft_voucher(): void
    {
        Permission::create(['name' => 'voucher.create']);
        Permission::create(['name' => 'voucher.post']);

        $accountant = User::factory()->create();
        $accountant->givePermissionTo('voucher.create');

        $approver = User::factory()->create();
        $approver->givePermissionTo('voucher.post');

        $account1 = ChartOfAccount::create(['code' => '1010', 'name' => 'Bank', 'type' => AccountType::Asset, 'normal_balance' => NormalBalance::Debit]);
        $account2 = ChartOfAccount::create(['code' => '5010', 'name' => 'Rent', 'type' => AccountType::Expense, 'normal_balance' => NormalBalance::Debit]);

        $createAction = new CreateJournalVoucherAction(new SequentialVoucherNumberGenerator);
        $voucher = $createAction->execute([
            'voucher_date' => '2026-07-23',
            'type' => VoucherType::Bank,
            'lines' => [
                ['account_id' => $account2->id, 'debit_minor' => 1500000, 'credit_minor' => 0],
                ['account_id' => $account1->id, 'debit_minor' => 0, 'credit_minor' => 1500000],
            ],
        ], $accountant);

        $postAction = new PostJournalVoucherAction;
        $postedVoucher = $postAction->execute($voucher, $approver);

        $this->assertEquals(VoucherStatus::Posted, $postedVoucher->status);
        $this->assertEquals($approver->id, $postedVoucher->posted_by);
        $this->assertNotNull($postedVoucher->posted_at);

        // GL net zero reconciliation assertion
        $this->assertEquals($postedVoucher->total_debit_minor, $postedVoucher->total_credit_minor);
    }

    public function test_user_without_post_permission_cannot_post_voucher(): void
    {
        $this->expectException(AuthorizationException::class);

        $accountant = User::factory()->create();
        $account1 = ChartOfAccount::create(['code' => '1010', 'name' => 'Bank', 'type' => AccountType::Asset, 'normal_balance' => NormalBalance::Debit]);
        $account2 = ChartOfAccount::create(['code' => '5010', 'name' => 'Rent', 'type' => AccountType::Expense, 'normal_balance' => NormalBalance::Debit]);

        $createAction = new CreateJournalVoucherAction(new SequentialVoucherNumberGenerator);
        $voucher = $createAction->execute([
            'voucher_date' => '2026-07-23',
            'type' => VoucherType::Bank,
            'lines' => [
                ['account_id' => $account2->id, 'debit_minor' => 1000, 'credit_minor' => 0],
                ['account_id' => $account1->id, 'debit_minor' => 0, 'credit_minor' => 1000],
            ],
        ], $accountant);

        $postAction = new PostJournalVoucherAction;
        $postAction->execute($voucher, $accountant); // Accountant lacks voucher.post permission
    }
}
