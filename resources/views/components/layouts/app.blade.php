<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'JournalGrid — Double-Entry Accounting' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-slate-100 bg-slate-950" x-data="{ showLogoutModal: false }">
    <div class="min-h-full flex flex-col">
        <!-- Top Navigation Bar -->
        <header class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-8">
                        <a href="{{ route('vouchers.index') }}" class="flex items-center space-x-3 group">
                            <img src="{{ asset('favicon.png') }}" alt="JournalGrid Logo" class="h-8 w-8 rounded-lg shadow-md transition-transform group-hover:scale-105">
                            <span class="text-xl font-bold tracking-wider text-emerald-400">Journal<span class="text-slate-100 font-light">Grid</span></span>
                        </a>

                        @auth
                            <nav class="hidden md:flex space-x-4 text-sm font-semibold">
                                <a href="{{ route('vouchers.index') }}"
                                   class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('vouchers.index') ? 'bg-slate-800 text-emerald-400' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                                    Vouchers
                                </a>
                                @can('voucher.create')
                                    <a href="{{ route('vouchers.create') }}"
                                       class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('vouchers.create') ? 'bg-slate-800 text-emerald-400' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                                        + New Entry
                                    </a>
                                @endcan
                                <a href="{{ route('reports.trial-balance') }}"
                                   class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('reports.trial-balance') ? 'bg-slate-800 text-emerald-400' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                                    Trial Balance
                                </a>
                                <a href="{{ route('reports.general-ledger') }}"
                                   class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('reports.general-ledger') ? 'bg-slate-800 text-emerald-400' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                                    General Ledger
                                </a>
                            </nav>
                        @endauth
                    </div>

                    @auth
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="text-right hidden sm:block">
                                <span class="block font-medium text-slate-200">{{ auth()->user()->name }}</span>
                                <span class="block text-xs text-emerald-400 uppercase tracking-wider">
                                    {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'User' }}
                                </span>
                            </div>
                            <button type="button" @click="showLogoutModal = true" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg border border-slate-700 transition-colors cursor-pointer">
                                Logout
                            </button>
                        </div>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>
    </div>

    @auth
        <!-- Logout Confirmation Modal -->
        <div x-cloak x-show="showLogoutModal" 
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop overlay -->
            <div x-show="showLogoutModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                 @click="showLogoutModal = false"></div>

            <!-- Modal Box -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showLogoutModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-xl bg-slate-900 border border-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="p-6">
                        <div class="flex items-center space-x-3 text-rose-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <h3 class="text-lg font-bold text-slate-100" id="modal-title">Confirm Logout</h3>
                        </div>
                        <p class="mt-3 text-sm text-slate-400">
                            Are you sure you want to log out of JournalGrid? You will need to sign in again to access your vouchers and reports.
                        </p>
                    </div>
                    <div class="bg-slate-950/60 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-slate-800">
                        <button type="button" @click="showLogoutModal = false"
                                class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full sm:w-auto px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-lg shadow-rose-950/50">
                                Yes, Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endauth

    @livewireScripts
</body>
</html>
