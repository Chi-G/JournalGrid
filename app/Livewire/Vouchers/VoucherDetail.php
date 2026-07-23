<?php

namespace App\Livewire\Vouchers;

use App\Actions\Vouchers\PostJournalVoucherAction;
use App\Actions\Vouchers\ReverseJournalVoucherAction;
use App\Models\JournalVoucher;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class VoucherDetail extends Component
{
    public JournalVoucher $voucher;

    public function mount(JournalVoucher $voucher): void
    {
        $this->authorize('view', $voucher);
        $this->voucher = $voucher->load(['lines.account', 'creator', 'poster', 'reversalOf']);
    }

    public function post(PostJournalVoucherAction $postAction)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $postAction->execute($this->voucher, $user);
            session()->flash('status', "Voucher #{$this->voucher->voucher_no} has been posted successfully.");
            $this->voucher->refresh();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reverse(ReverseJournalVoucherAction $reverseAction)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $reversal = $reverseAction->execute($this->voucher, $user);
            session()->flash('status', "Voucher #{$this->voucher->voucher_no} reversed via Reversal Voucher #{$reversal->voucher_no}.");

            return redirect()->route('vouchers.show', $reversal);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.vouchers.voucher-detail');
    }
}
