<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">Journal Vouchers Register</h1>
            <p class="text-sm text-slate-400">View, search, post, and reverse double-entry vouchers.</p>
        </div>
        @can('voucher.create')
            <a href="{{ route('vouchers.create') }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm rounded-lg shadow-lg hover:shadow-emerald-500/20 transition-all cursor-pointer">
                + New Journal Voucher
            </a>
        @endcan
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

    <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl space-y-4">
        <x-laragrid :grid="$grids['vouchers']" />
    </div>

    <!-- Actions Panel for Vouchers -->
    <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl space-y-4">
        <h2 class="text-lg font-semibold text-slate-200">Voucher Lifecycle Controls</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-800 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Voucher No</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Narration</th>
                        <th class="px-4 py-3">Total (NGN)</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($vouchersList as $voucher)
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-emerald-400">
                                <a href="{{ route('vouchers.show', $voucher) }}" class="hover:underline">
                                    {{ $voucher->voucher_no }}
                                </a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $voucher->voucher_date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 uppercase text-xs font-semibold">{{ $voucher->type->value }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ Str::limit($voucher->narration, 40) ?: '—' }}</td>
                            <td class="px-4 py-3 font-mono font-semibold">{{ number_format($voucher->total_debit_minor / 100, 2) }}</td>
                            <td class="px-4 py-3">
                                @if($voucher->status->value === 'posted')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-950 text-emerald-400 border border-emerald-800 uppercase">Posted</span>
                                @elseif($voucher->status->value === 'reversed')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-950 text-amber-400 border border-amber-800 uppercase">Reversed</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-800 text-slate-300 border border-slate-700 uppercase">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('vouchers.show', $voucher) }}" class="px-2.5 py-1 text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded transition-colors">
                                    View
                                </a>
                                @if($voucher->status->value === 'draft')
                                    @can('voucher.post')
                                        <button wire:click="postVoucher({{ $voucher->id }})" wire:confirm="Are you sure you want to post Voucher {{ $voucher->voucher_no }}? Posted vouchers cannot be edited."
                                            class="px-2.5 py-1 text-xs bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold rounded transition-colors cursor-pointer">
                                            Post
                                        </button>
                                    @endcan
                                @elseif($voucher->status->value === 'posted')
                                    @can('voucher.reverse')
                                        <button wire:click="reverseVoucher({{ $voucher->id }})" wire:confirm="Are you sure you want to REVERSE Voucher {{ $voucher->voucher_no }}? This will create a mirrored reversal entry."
                                            class="px-2.5 py-1 text-xs bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded transition-colors cursor-pointer">
                                            Reverse
                                        </button>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                No journal vouchers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pt-2">
            {{ $vouchersList->links() }}
        </div>
    </div>
</div>
