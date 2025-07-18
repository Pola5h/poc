<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;

// Customize detection logic
InitializeTenancyByRequestData::$header = 'X-TENANT-ID';
InitializeTenancyByRequestData::$queryParameter = null; // or null to disable query param

Route::middleware([
    'api',
    
    InitializeTenancyByRequestData::class,
    
])->group(function () {
    Route::get('/', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });

Route::get('/debug-tenancy', function () {
 return [
        'tenant' => tenant(),
        'tenant_data' => tenant()?->toArray(),
        'db_name' => DB::getDatabaseName(),
        'user' => DB::getConfig('username'),
        'tenancy_initialized' => app(\Stancl\Tenancy\Tenancy::class)->initialized,
    ];
});

Route::get('/employees-count', function () {
    $tenant = tenant();
    if ($tenant && $tenant->database) {
        // Copy central connection config
        $centralConfig = config('database.connections.central');
        $centralConfig['database'] = $tenant->database;
        $centralConfig['username'] = $tenant->username;
        $centralConfig['password'] = $tenant->password;
        // Set the full config for tenant connection
        config(['database.connections.tenant' => $centralConfig]);
        // Now use the tenant connection
        $count = DB::connection('tenant')->table('employees')->count();
        return ['employees_count' => $count];
    }
    return response()->json(['error' => 'Tenant DB not found'], 404);
});
Route::get('/check-db', function () {
    return [
        'tenant_id' => tenant()?->id,
        'db' => DB::connection()->getDatabaseName(),
    ];
});

});
