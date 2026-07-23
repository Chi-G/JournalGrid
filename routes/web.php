<?php

use App\Livewire\Auth\Login;
use App\Livewire\Reports\GeneralLedger;
use App\Livewire\Reports\TrialBalance;
use App\Livewire\Vouchers\VoucherDetail;
use App\Livewire\Vouchers\VoucherEntry;
use App\Livewire\Vouchers\VoucherList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('vouchers.index') : redirect()->route('login');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/vouchers', VoucherList::class)->name('vouchers.index');
    Route::get('/vouchers/create', VoucherEntry::class)->name('vouchers.create');
    Route::get('/vouchers/{voucher}', VoucherDetail::class)->name('vouchers.show');

    Route::get('/reports/trial-balance', TrialBalance::class)->name('reports.trial-balance');
    Route::get('/reports/general-ledger', GeneralLedger::class)->name('reports.general-ledger');
});
