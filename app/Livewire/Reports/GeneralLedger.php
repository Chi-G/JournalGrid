<?php

namespace App\Livewire\Reports;

use App\Enums\VoucherStatus;
use App\Models\ChartOfAccount;
use App\Models\JournalLine;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class GeneralLedger extends Component
{
    use WithPagination;

    public ?int $selectedAccountId = null;

    public function updatedSelectedAccountId(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $firstAccount = ChartOfAccount::where('is_postable', true)->where('is_active', true)->orderBy('code')->first();
        $this->selectedAccountId = $firstAccount?->id;
    }

    public function render(): View
    {
        $accounts = ChartOfAccount::where('is_postable', true)->where('is_active', true)->orderBy('code')->get();

        $selectedAccount = $this->selectedAccountId ? ChartOfAccount::find($this->selectedAccountId) : null;

        $lines = collect();
        if ($selectedAccount) {
            $lines = JournalLine::query()
                ->where('account_id', $selectedAccount->id)
                ->whereHas('voucher', fn ($q) => $q->where('status', VoucherStatus::Posted->value))
                ->with('voucher')
                ->paginate(5);
        }

        return view('livewire.reports.general-ledger', [
            'accounts' => $accounts,
            'selectedAccount' => $selectedAccount,
            'lines' => $lines,
        ]);
    }
}
