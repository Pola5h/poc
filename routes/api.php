<?php

declare(strict_types=1);

use App\Models\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;

// Customize detection logic
InitializeTenancyByRequestData::$header = 'X-TENANT-ID';
InitializeTenancyByRequestData::$queryParameter = null; // or null to disable query param

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
        if ($tenant && $tenancy->initialized && $dbName === $tenant->database && $dbUser === $tenant->username) {
            $isSwitched = true;
        }
        return [
            'tenant_id' => $tenant?->id,
            'expected_db' => $tenant?->database,
            'expected_user' => $tenant?->username,
            'current_db' => $dbName,
            'current_user' => $dbUser,
            'tenancy_initialized' => $tenancy->initialized,
            'connection_switched' => $isSwitched,
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
