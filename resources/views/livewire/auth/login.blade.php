<div class="max-w-md mx-auto my-12 p-8 bg-slate-900 border border-slate-800 rounded-xl shadow-2xl text-slate-100">
    <div class="text-center mb-8 flex flex-col items-center">
        <img src="{{ asset('logo.png') }}" alt="JournalGrid Logo" class="h-16 w-16 rounded-xl shadow-lg mb-3">
        <h1 class="text-3xl font-bold tracking-tight text-emerald-400">JournalGrid</h1>
        <p class="text-sm text-slate-400 mt-1">Double-Entry Accounting & General Ledger</p>
    </div>

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Email Address</label>
            <input type="email" id="email" wire:model="email" required
                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
            @error('email') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Password</label>
            <input type="password" id="password" wire:model="password" required
                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
            @error('password') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center space-x-2 text-slate-400 cursor-pointer">
                <input type="checkbox" wire:model="remember" class="rounded border-slate-700 bg-slate-800 text-emerald-500 focus:ring-emerald-500">
                <span>Remember me</span>
            </label>
        </div>

        <button type="submit"
            class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-semibold rounded-lg shadow-lg hover:shadow-emerald-500/20 transition-all cursor-pointer">
            Sign In
        </button>
    </form>

    <div class="mt-8 pt-6 border-t border-slate-800">
        <p class="text-xs font-medium text-slate-400 text-center mb-3">Quick Demo Login Presets</p>
        <div class="grid grid-cols-2 gap-2">
            <button type="button" wire:click="quickLogin('accountant@journalgrid.com')"
                class="px-3 py-2 text-xs bg-slate-800 hover:bg-slate-700 border border-slate-700 text-emerald-400 font-medium rounded transition-colors text-left">
                <span class="block text-slate-200 font-bold">Accountant</span>
                <span>(Create Drafts)</span>
            </button>
            <button type="button" wire:click="quickLogin('approver@journalgrid.com')"
                class="px-3 py-2 text-xs bg-slate-800 hover:bg-slate-700 border border-slate-700 text-blue-400 font-medium rounded transition-colors text-left">
                <span class="block text-slate-200 font-bold">Approver</span>
                <span>(Post/Reverse)</span>
            </button>
            <button type="button" wire:click="quickLogin('auditor@journalgrid.com')"
                class="px-3 py-2 text-xs bg-slate-800 hover:bg-slate-700 border border-slate-700 text-amber-400 font-medium rounded transition-colors text-left">
                <span class="block text-slate-200 font-bold">Auditor</span>
                <span>(Read Only)</span>
            </button>
            <button type="button" wire:click="quickLogin('admin@journalgrid.com')"
                class="px-3 py-2 text-xs bg-slate-800 hover:bg-slate-700 border border-slate-700 text-purple-400 font-medium rounded transition-colors text-left">
                <span class="block text-slate-200 font-bold">Admin</span>
                <span>(Full Access)</span>
            </button>
        </div>
    </div>
</div>
