<?php

declare(strict_types=1);

use App\Models\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;

// Customize detection logic
InitializeTenancyByRequestData::$header = 'X-TENANT-ID';
InitializeTenancyByRequestData::$queryParameter = null; // or null to disable query param

Route::get('/tenant-info', function () {
    $tenant = Tenant::find('tenant1');
    if ($tenant) {
        return response()->json([
            'id' => $tenant->getTenantKey(),
            'database' => $tenant->database,
            'username' => $tenant->username,
        ]);
    }
    return response()->json(['error' => 'No tenant found'], 404);
})->withoutMiddleware(InitializeTenancyByRequestData::class);


Route::middleware([
    'api'
])->group(function () {
    Route::get('/', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });
    
    Route::get('/debug-tenancy', function () {
        $tenant = tenant();
        $tenancy = app(\Stancl\Tenancy\Tenancy::class);
        $isSwitched = false;
        $dbName = DB::connection()->getDatabaseName();
        $dbUser = DB::getConfig('username');

        return [
            'tenant_id' => $tenant?->id,
            'current_db' => $dbName,
            'current_user' => $dbUser,
            'tenancy_initialized' => $tenancy->initialized,
            'connection_switched' => $isSwitched,
        ];
    });
    Route::get('/employees-count', function () {
    
            // Now use the tenant connection
            $count = DB::table('employees')->count();
            return ['employees_count' => $count];
    });
    Route::get('/check-db', function () {
        return [
            'tenant_id' => tenant()?->id,
            'db' => DB::connection()->getDatabaseName(),
        ];
    });
});
