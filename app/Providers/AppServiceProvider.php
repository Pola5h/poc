<?php

namespace App\Providers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenancyInitialized;
use Illuminate\Support\Facades\Event;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    Event::listen(TenancyInitialized::class, function () {
        $tenant = tenancy()->tenant;
        Log::info("Tenancy initialized for tenant {$tenant->id}");
    });

    }
}
