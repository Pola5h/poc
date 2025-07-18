<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\DatabaseConfig;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;

    public function databaseConfig(): \Stancl\Tenancy\DatabaseConfig
    {
        return new \Stancl\Tenancy\DatabaseConfig([
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => $this->getAttribute('database') ?? '',
            'username' => $this->getAttribute('username') ?? '',
            'password' => $this->getAttribute('password') ?? '',
        ]);
    }
    
}
