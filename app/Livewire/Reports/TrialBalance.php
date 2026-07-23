<?php

namespace App\Livewire\Reports;

use App\Models\JournalVoucher;
use App\Services\TrialBalanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use LaraGrid\Columns\ComputedColumn;
use LaraGrid\Columns\SerialColumn;
use LaraGrid\Columns\TextColumn;
use LaraGrid\Grid;
use LaraGrid\Livewire\WithLaraGrid;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class TrialBalance extends Component
{
    use WithLaraGrid, WithPagination;

    /** @var list<array<string, mixed>> */
    public array $tbRows = [];

    public function mount(TrialBalanceService $service): void
    {
        $this->authorize('viewAny', JournalVoucher::class);
        $this->refreshTrialBalance($service);
    }

    public function refreshTrialBalance(TrialBalanceService $service): void
    {
        $rows = $service->generate()->toArray();
        $this->tbRows = array_map(function ($row) {
            $row['_k'] = (string) $row['account_id'];
            $row['debit_formatted'] = number_format($row['net_debit_minor'] / 100, 2);
            $row['credit_formatted'] = number_format($row['net_credit_minor'] / 100, 2);

            return $row;
        }, $rows);

        $this->reseedGrid('tb', $this->tbRows);
        $this->resetPage();
    }

    protected function grids(): array
    {
        return [
            'tb' => Grid::make('tb')
                ->columns([
                    SerialColumn::make(),
                    TextColumn::make('code')->label('Code')->width(80),
                    TextColumn::make('name')->label('Account Name')->grow(),
                    TextColumn::make('type')->label('Type')->width(90),
                    TextColumn::make('normal_balance')->label('Normal')->width(90),
                    ComputedColumn::make('debit_formatted')->label('Net Debit (NGN)'),
                    ComputedColumn::make('credit_formatted')->label('Net Credit (NGN)'),
                ])
                ->striped(),
        ];
    }

    public function render(): View
    {
        $currentPage = $this->getPage();
        $perPage = 5;
        $collection = collect($this->tbRows);

        $grandTotalDebit = $collection->sum('net_debit_minor');
        $grandTotalCredit = $collection->sum('net_credit_minor');

        $paginatedRows = new LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage)->values(),
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('livewire.reports.trial-balance', [
            'grids' => $this->resolvedGrids(),
            'paginatedRows' => $paginatedRows,
            'grandTotalDebit' => $grandTotalDebit,
            'grandTotalCredit' => $grandTotalCredit,
        ]);
    }
}
