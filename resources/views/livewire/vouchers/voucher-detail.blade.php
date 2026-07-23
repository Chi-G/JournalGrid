<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold font-mono text-emerald-400">Voucher #{{ $voucher->voucher_no }}</h1>
                @if($voucher->status->value === 'posted')
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-950 text-emerald-400 border border-emerald-800 uppercase tracking-wider">Posted</span>
                @elseif($voucher->status->value === 'reversed')
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-950 text-amber-400 border border-amber-800 uppercase tracking-wider">Reversed</span>
                @else
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-800 text-slate-300 border border-slate-700 uppercase tracking-wider">Draft</span>
                @endif
            </div>
            <p class="text-sm text-slate-400 mt-1">Created on {{ $voucher->created_at->format('M d, Y') }} by {{ $voucher->creator?->name }}</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('vouchers.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition-colors">
                &larr; Back to List
            </a>

            @if($voucher->status->value === 'draft')
                @can('voucher.post')
                    <button wire:click="post" wire:confirm="Post Voucher {{ $voucher->voucher_no }}? Posted vouchers are locked and immutable."
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm rounded-lg shadow-lg hover:shadow-emerald-500/20 transition-all cursor-pointer">
                        Post Voucher
                    </button>
                @endcan
            @elseif($voucher->status->value === 'posted')
                @can('voucher.reverse')
                    <button wire:click="reverse" wire:confirm="Reverse Voucher {{ $voucher->voucher_no }}? A new reversal entry will be generated."
                        class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold text-sm rounded-lg shadow-lg hover:shadow-amber-500/20 transition-all cursor-pointer">
                        Reverse Voucher
                    </button>
                @endcan
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 bg-emerald-950/60 border border-emerald-800 text-emerald-300 rounded-lg text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-rose-950/60 border border-rose-800 text-rose-300 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header Details Card -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6 bg-slate-900 border border-slate-800 rounded-xl text-sm">
        <div>
            <span class="block text-xs uppercase tracking-wider font-semibold text-slate-500">Voucher Date</span>
            <span class="font-medium text-slate-200">{{ $voucher->voucher_date->format('Y-m-d') }}</span>
        </div>
        <div>
            <span class="block text-xs uppercase tracking-wider font-semibold text-slate-500">Voucher Type</span>
            <span class="font-medium text-slate-200 uppercase">{{ $voucher->type->value }}</span>
        </div>
        <div>
            <span class="block text-xs uppercase tracking-wider font-semibold text-slate-500">Posted By</span>
            <span class="font-medium text-slate-200">{{ $voucher->poster?->name ?? '— (Not Posted)' }}</span>
        </div>
        <div>
            <span class="block text-xs uppercase tracking-wider font-semibold text-slate-500">Reversal Status</span>
            @if($voucher->reversalOf)
                <span class="font-medium text-amber-400">Reversal of {{ $voucher->reversalOf->voucher_no }}</span>
            @else
                <span class="font-medium text-slate-400">Original Voucher</span>
            @endif
        </div>
        <div class="md:col-span-4">
            <span class="block text-xs uppercase tracking-wider font-semibold text-slate-500">General Narration</span>
            <span class="font-medium text-slate-200">{{ $voucher->narration ?: '—' }}</span>
        </div>
    </div>

    <!-- Lines Table -->
    <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl space-y-4">
        <h2 class="text-lg font-semibold text-slate-200">Journal Lines</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-800 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Account Code & Name</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Line Narration</th>
                        <th class="px-4 py-3 text-right">Debit (NGN)</th>
                        <th class="px-4 py-3 text-right">Credit (NGN)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($voucher->lines as $line)
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 text-slate-500 font-mono">{{ $line->line_order }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-200">
                                {{ $line->account?->code }} - {{ $line->account?->name }}
                            </td>
                            <td class="px-4 py-3 text-xs uppercase text-slate-400">{{ $line->account?->type->value }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $line->narration ?: '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-slate-100">
                                {{ $line->debit_minor > 0 ? number_format($line->debit_minor / 100, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-slate-100">
                                {{ $line->credit_minor > 0 ? number_format($line->credit_minor / 100, 2) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-800/80 font-bold border-t border-slate-700">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right text-xs uppercase tracking-wider text-slate-400">Total Reconciled Balance</td>
                        <td class="px-4 py-3 text-right font-mono text-emerald-400 font-bold text-base">
                            {{ number_format($voucher->total_debit_minor / 100, 2) }}
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-emerald-400 font-bold text-base">
                            {{ number_format($voucher->total_credit_minor / 100, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
