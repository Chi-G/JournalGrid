<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">Trial Balance Report</h1>
            <p class="text-sm text-slate-400">Aggregated net balances across all postable accounts (&Sigma;Debit = &Sigma;Credit).</p>
        </div>
        <button wire:click="refreshTrialBalance" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold rounded-lg transition-colors cursor-pointer">
            &circlearrowright; Recalculate Report
        </button>
    </div>

    <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl space-y-4">
        <h2 class="text-lg font-semibold text-slate-200">Display-Mode Aggregate Datagrid</h2>
        <x-laragrid :grid="$grids['tb']" :rows="$tbRows" />
    </div>

    <!-- Summary Balance Table -->
    <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl space-y-4">
        <h2 class="text-lg font-semibold text-slate-200">General Ledger Reconciled Summary</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-800 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Account Name</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Normal Balance</th>
                        <th class="px-4 py-3 text-right">Debit (NGN)</th>
                        <th class="px-4 py-3 text-right">Credit (NGN)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($paginatedRows as $row)
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-emerald-400 font-bold">{{ $row['code'] }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-200">{{ $row['name'] }}</td>
                            <td class="px-4 py-3 text-xs uppercase text-slate-400">{{ $row['type'] }}</td>
                            <td class="px-4 py-3 text-xs uppercase text-slate-400">{{ $row['normal_balance'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-slate-100">
                                {{ $row['net_debit_minor'] > 0 ? number_format($row['net_debit_minor'] / 100, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-slate-100">
                                {{ $row['net_credit_minor'] > 0 ? number_format($row['net_credit_minor'] / 100, 2) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-800/80 font-bold border-t border-slate-700">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right text-xs uppercase tracking-wider text-slate-400">Grand Total Net Reconciled Balance</td>
                        <td class="px-4 py-3 text-right font-mono text-emerald-400 font-bold text-base">
                            {{ number_format($grandTotalDebit / 100, 2) }}
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-emerald-400 font-bold text-base">
                            {{ number_format($grandTotalCredit / 100, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="pt-2">
            {{ $paginatedRows->links() }}
        </div>
    </div>
</div>
