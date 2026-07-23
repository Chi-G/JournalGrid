<?php

namespace App\Livewire\Vouchers;

use App\Actions\Vouchers\PostJournalVoucherAction;
use App\Actions\Vouchers\ReverseJournalVoucherAction;
use App\Models\JournalVoucher;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use LaraGrid\Columns\ComputedColumn;
use LaraGrid\Columns\DateColumn;
use LaraGrid\Columns\SerialColumn;
use LaraGrid\Columns\TextColumn;
use LaraGrid\Filters\SelectFilter;
use LaraGrid\Grid;
use LaraGrid\Livewire\WithLaraGrid;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class VoucherList extends Component
{
    use WithLaraGrid;

    public function mount(): void
    {
        $this->authorize('viewAny', JournalVoucher::class);
    }

    protected function grids(): array
    {
        return [
            'vouchers' => Grid::make('vouchers')
                ->query(fn () => JournalVoucher::with('creator'))
                ->authorize(fn (): bool => Auth::user()?->can('voucher.view') ?? false)
                ->defaultSort('voucher_date', 'desc')
                ->searchable(['voucher_no', 'narration'])
                ->paginate(5, [5, 10, 25])
                ->filters([
                    SelectFilter::make('status')->label('Status')->options([
                        'draft' => 'Draft',
                        'posted' => 'Posted',
                        'reversed' => 'Reversed',
                    ]),
                    SelectFilter::make('type')->label('Type')->options([
                        'general' => 'General (JV)',
                        'cash' => 'Cash (CPV)',
                        'bank' => 'Bank (BPV)',
                        'adjustment' => 'Adjustment (ADJ)',
                    ]),
                ])
                ->savedViews('vouchers-list')
                ->exportable(['csv', 'xlsx'], fileName: 'journal-vouchers')
                ->columns([
                    SerialColumn::make(),
                    TextColumn::make('voucher_no')->label('Voucher No')->sortable()->searchable(),
                    DateColumn::make('voucher_date')->label('Date')->sortable(),
                    TextColumn::make('type')->label('Type')->sortable(),
                    TextColumn::make('narration')->label('Narration')->grow(),
                    ComputedColumn::make('amount')->label('Total Amount')->state(function (array $row) {
                        return 'NGN '.number_format(($row['total_debit_minor'] ?? 0) / 100, 2);
                    }),
                    ComputedColumn::make('status_badge')->label('Status')->html()->state(function (array $row) {
                        $status = $row['status'] ?? 'draft';
                        $color = match ($status) {
                            'posted' => 'bg-emerald-950/60 text-emerald-400 border-emerald-800/60',
                            'reversed' => 'bg-amber-950/60 text-amber-400 border-amber-800/60',
                            default => 'bg-slate-800 text-slate-300 border-slate-700',
                        };

                        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border {$color}\">{$status}</span>";
                    }),
                ]),
        ];
    }

    public function postVoucher(JournalVoucher $voucher, PostJournalVoucherAction $postAction)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $postAction->execute($voucher, $user);
            session()->flash('status', "Voucher #{$voucher->voucher_no} has been posted successfully.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reverseVoucher(JournalVoucher $voucher, ReverseJournalVoucherAction $reverseAction)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $reversal = $reverseAction->execute($voucher, $user);
            session()->flash('status', "Voucher #{$voucher->voucher_no} reversed via Reversal Voucher #{$reversal->voucher_no}.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(): View
    {
        $vouchersQuery = JournalVoucher::with('creator')->latest()->paginate(5);

        return view('livewire.vouchers.voucher-list', [
            'grids' => $this->resolvedGrids(),
            'vouchersList' => $vouchersQuery,
        ]);
    }
}
