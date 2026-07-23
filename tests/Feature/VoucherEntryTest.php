<?php

namespace Tests\Feature;

use App\Livewire\Vouchers\VoucherEntry;
use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class VoucherEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_accountant_can_render_and_submit_voucher_entry(): void
    {
        Permission::create(['name' => 'voucher.create']);
        Permission::create(['name' => 'voucher.view']);

        $user = User::factory()->create();
        $user->givePermissionTo(['voucher.create', 'voucher.view']);

        $account1 = ChartOfAccount::create(['code' => '1010', 'name' => 'Cash', 'type' => 'asset', 'normal_balance' => 'debit', 'is_postable' => true]);
        $account2 = ChartOfAccount::create(['code' => '4010', 'name' => 'Sales', 'type' => 'income', 'normal_balance' => 'credit', 'is_postable' => true]);

        Livewire::actingAs($user)
            ->test(VoucherEntry::class)
            ->set('voucher_date', '2026-07-23')
            ->set('type', 'general')
            ->set('narration', 'Feature test voucher')
            ->set('lines', [
                ['_k' => 'l1', 'account_id' => $account1->id, 'debit_amount' => '100.00', 'credit_amount' => '0.00', 'narration' => 'Cash Dr'],
                ['_k' => 'l2', 'account_id' => $account2->id, 'debit_amount' => '0.00', 'credit_amount' => '100.00', 'narration' => 'Sales Cr'],
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('journal_vouchers', [
            'narration' => 'Feature test voucher',
            'total_debit_minor' => 10000,
            'total_credit_minor' => 10000,
        ]);
    }
}
