<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">General Ledger Account Detail</h1>
            <p class="text-sm text-slate-400">Audit posted transactions and running balances per account.</p>
        </div>

        <div class="w-full sm:w-80">
            <select wire:model.live="selectedAccountId"
                class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ strtoupper($acc->type->value) }})</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($selectedAccount)
        <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-xl font-bold text-emerald-400">{{ $selectedAccount->code }} - {{ $selectedAccount->name }}</h2>
                    <p class="text-xs uppercase text-slate-400 mt-0.5">Type: <span class="text-slate-200 font-semibold">{{ $selectedAccount->type->value }}</span> | Normal Balance: <span class="text-slate-200 font-semibold">{{ $selectedAccount->normal_balance->value }}</span></p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800 text-xs font-semibold uppercase text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Voucher No</th>
                            <th class="px-4 py-3">Narration</th>
                            <th class="px-4 py-3 text-right">Debit (NGN)</th>
                            <th class="px-4 py-3 text-right">Credit (NGN)</th>
                            <th class="px-4 py-3 text-right">Running Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @php
                            $runningBalance = 0;
                            $normal = $selectedAccount->normal_balance->value;
                        @endphp
                        @forelse($lines as $line)
                            @php
                                $debit = $line->debit_minor;
                                $credit = $line->credit_minor;
                                if ($normal === 'debit') {
                                    $runningBalance += ($debit - $credit);
                                } else {
                                    $runningBalance += ($credit - $debit);
                                }
                            @endphp
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-slate-400">{{ $line->voucher->voucher_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-emerald-400">
                                    <a href="{{ route('vouchers.show', $line->voucher) }}" class="hover:underline">
                                        {{ $line->voucher->voucher_no }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ $line->narration ?: $line->voucher->narration }}</td>
                                <td class="px-4 py-3 text-right font-mono text-slate-100">
                                    {{ $debit > 0 ? number_format($debit / 100, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-slate-100">
                                    {{ $credit > 0 ? number_format($credit / 100, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-emerald-400">
                                    {{ number_format($runningBalance / 100, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                    No posted transactions for this account.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($lines, 'links'))
                <div class="pt-2">
                    {{ $lines->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
