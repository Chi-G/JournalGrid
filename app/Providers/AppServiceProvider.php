<?php

namespace App\Providers;

use App\Contracts\VoucherNumberGenerator;
use App\Services\SequentialVoucherNumberGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            VoucherNumberGenerator::class,
            SequentialVoucherNumberGenerator::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
