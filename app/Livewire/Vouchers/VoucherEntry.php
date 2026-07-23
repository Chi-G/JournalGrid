<?php

namespace App\Livewire\Vouchers;

use App\Actions\Vouchers\CreateJournalVoucherAction;
use App\Enums\VoucherType;
use App\Models\ChartOfAccount;
use App\Models\JournalVoucher;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use LaraGrid\Columns\DecimalColumn;
use LaraGrid\Columns\SearchSelectColumn;
use LaraGrid\Columns\SerialColumn;
use LaraGrid\Columns\TextColumn;
use LaraGrid\Grid;
use LaraGrid\Livewire\WithLaraGrid;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class VoucherEntry extends Component
{
    use WithLaraGrid;

    public string $voucher_date = '';

    public string $type = 'general';

    public ?string $narration = null;

    /** @var list<array<string, mixed>> */
    public array $lines = [];

    public function mount(): void
    {
        $this->authorize('create', JournalVoucher::class);
        $this->voucher_date = now()->toDateString();
        $this->lines = [
            ['_k' => 'l_'.bin2hex(random_bytes(4)), 'account_id' => null, 'narration' => '', 'debit_amount' => '0.00', 'credit_amount' => '0.00'],
            ['_k' => 'l_'.bin2hex(random_bytes(4)), 'account_id' => null, 'narration' => '', 'debit_amount' => '0.00', 'credit_amount' => '0.00'],
        ];
    }

    public function hydrate(): void
    {
        foreach ($this->lines as &$line) {
            if (is_array($line) && empty($line['_k'])) {
                $line['_k'] = 'l_'.bin2hex(random_bytes(4));
            }
        }
    }

    public function addLine(): void
    {
        $this->lines[] = [
            '_k' => 'l_'.bin2hex(random_bytes(4)),
            'account_id' => null,
            'narration' => '',
            'debit_amount' => '0.00',
            'credit_amount' => '0.00',
        ];
        $this->reseedGrid('lines', $this->lines);
    }

    public function removeLine(int $index): void
    {
        if (count($this->lines) > 2) {
            unset($this->lines[$index]);
            $this->lines = array_values($this->lines);
            $this->reseedGrid('lines', $this->lines);
        }
    }

    protected function grids(): array
    {
        return [
            'lines' => Grid::make('lines')
                ->editable()
                ->rowsFrom('lines')
                ->authorize(fn (): bool => Auth::user()?->can('voucher.create') ?? false)
                ->minRows(1)
                ->defaultRows(2)
                ->autoAppend()
                ->completeWhenBalanced('debit_amount', 'credit_amount')
                ->newRowUsing(fn (): array => [
                    '_k' => 'l_'.bin2hex(random_bytes(4)),
                    'account_id' => null,
                    'narration' => '',
                    'debit_amount' => '0.00',
                    'credit_amount' => '0.00',
                ])
                ->columns([
                    SerialColumn::make(),
                    SearchSelectColumn::make('account_id')
                        ->label('Account')
                        ->options(ChartOfAccount::query()
                            ->where('is_postable', true)
                            ->where('is_active', true)
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn ($acc) => [$acc->id => "{$acc->code} - {$acc->name} ({$acc->type->value})"])
                            ->toArray())
                        ->grow(),
                    TextColumn::make('narration')->label('Line Narration'),
                    DecimalColumn::make('debit_amount')->label('Debit (NGN)')->scale(2),
                    DecimalColumn::make('credit_amount')->label('Credit (NGN)')->scale(2),
                ]),
        ];
    }

    public function save(CreateJournalVoucherAction $createAction)
    {
        $this->authorize('create', JournalVoucher::class);

        $this->validate([
            'voucher_date' => ['required', 'date'],
            'type' => ['required', 'string'],
        ]);

        $formattedLines = [];
        foreach ($this->lines as $index => $line) {
            $accountId = $line['account_id'] ?? null;
            $debitAmount = (float) ($line['debit_amount'] ?? 0);
            $creditAmount = (float) ($line['credit_amount'] ?? 0);
            $lineNumber = $index + 1;

            if (empty($accountId) && $debitAmount == 0 && $creditAmount == 0) {
                continue; // ignore blank auto-appended line
            }

            if (empty($accountId)) {
                $this->addError('general', "Line #{$lineNumber}: Please select an account from the dropdown.");

                return;
            }

            $rawAccountId = trim((string) $accountId);
            $codePrefix = trim(explode(' - ', $rawAccountId)[0]);

            $account = ChartOfAccount::query()
                ->where('id', $accountId)
                ->orWhere('code', $rawAccountId)
                ->orWhere('code', $codePrefix)
                ->first();

            if (! $account) {
                $this->addError('general', "Line #{$lineNumber}: Account '{$accountId}' is invalid.");

                return;
            }

            $formattedLines[] = [
                'account_id' => $account->id,
                'narration' => $line['narration'] ?? null,
                'debit_minor' => (int) round($debitAmount * 100),
                'credit_minor' => (int) round($creditAmount * 100),
            ];
        }

        // Simulate processing delay for loading spinner UX
        usleep(1000000);

        if (count($formattedLines) < 2) {
            $this->addError('general', 'A journal voucher must have at least 2 valid line items with selected accounts.');

            return;
        }

        try {
            /** @var User $user */
            $user = Auth::user();

            $voucher = $createAction->execute([
                'voucher_date' => $this->voucher_date,
                'type' => VoucherType::from($this->type),
                'narration' => $this->narration,
                'lines' => $formattedLines,
            ], $user);

            session()->flash('status', "Journal Voucher #{$voucher->voucher_no} created successfully in draft.");

            return redirect()->route('vouchers.show', $voucher);
        } catch (\Exception $e) {
            $this->addError('general', $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.vouchers.voucher-entry', [
            'grids' => $this->resolvedGrids(),
        ]);
    }
}
