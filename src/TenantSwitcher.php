<?php

namespace Dersam\Multitenant;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class TenantSwitcher
{
    /**
     * We have to inject app, as it has the methods for modifying the global config.
     * No real way around it. Don't make a habit of this.
     * @var Application $app
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function switchGlobalTenant(Tenant $tenant)
    {
        $repository = $this->app->make('config');
        $config = $repository->get("database.connections.tenant", null);
        if (is_null($config)) {
            throw new InvalidArgumentException("Database connection [tenant] is not available.");
        }

        Config::set('database.connections.tenant.database', 'tenant_' . $tenant->id);
    }
}