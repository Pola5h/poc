<?php

namespace App\Providers;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenancyInitialized;

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
         // Listen for tenancy initialization
    \Event::listen(TenancyInitialized::class, function ($event) {
        $tenant = Tenant::find('tenant1');

        $dbName = DB::connection()->getDatabaseName();
        $tenantId = tenant()?->id;
        Log::info("✅ Tenancy initialized for [{$tenantId}] using DB [{$dbName}], tenant data:[$tenant]");
    });
    }
}
