<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">New Journal Voucher</h1>
            <p class="text-sm text-slate-400">Enter header information and balanced line items.</p>
        </div>
        <a href="{{ route('vouchers.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition-colors">
            &larr; Back to List
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-950/80 border border-rose-800 text-rose-200 rounded-xl text-sm space-y-1 shadow-lg">
            <div class="font-bold flex items-center space-x-2 text-rose-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Submission Error</span>
            </div>
            <ul class="list-disc list-inside text-xs text-rose-300 pl-2 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-slate-900 border border-slate-800 rounded-xl">
            <div>
                <label for="voucher_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Voucher Date</label>
                <input type="date" id="voucher_date" wire:model="voucher_date" required
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                @error('voucher_date') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="type" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Voucher Type</label>
                <select id="type" wire:model="type" required
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="general">General Journal (JV)</option>
                    <option value="cash">Cash Payment (CPV)</option>
                    <option value="bank">Bank Payment (BPV)</option>
                    <option value="adjustment">Adjustment (ADJ)</option>
                </select>
                @error('type') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="narration" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">General Narration</label>
                <input type="text" id="narration" wire:model="narration" placeholder="e.g. Monthly rent allocation"
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
        </div>

        <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-200">Journal Lines Entry</h2>
                    <p class="text-xs text-slate-400">Select accounts and balance your debits and credits.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" wire:click="addLine" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-emerald-400 text-xs font-semibold rounded-lg border border-slate-700 transition-colors cursor-pointer">
                        + Add Line
                    </button>
                    <span class="text-xs text-emerald-400 bg-emerald-950/50 border border-emerald-800/50 px-3 py-1 rounded-full font-mono">
                        Double-Entry Guard Enabled (&Sigma;Debit = &Sigma;Credit)
                    </span>
                </div>
            </div>

            <x-laragrid :grid="$grids['lines']" :rows="$lines" />
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('vouchers.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold rounded-lg transition-colors">
                Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="inline-flex items-center space-x-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-slate-950 font-bold text-sm rounded-lg shadow-lg hover:shadow-emerald-500/20 transition-all cursor-pointer">
                <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="save">Save Draft Voucher</span>
                <span wire:loading wire:target="save">Processing Voucher...</span>
            </button>
        </div>
    </form>
</div>
