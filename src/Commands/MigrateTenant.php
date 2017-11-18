<?php

namespace Dersam\Multitenant\Commands;

use Dersam\Multitenant\Tenant;
use Dersam\Multitenant\TenantSwitcher;
use Illuminate\Console\Command;

class MigrateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multitenant:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for a specific tenant.';
    /**
     * @var TenantSwitcher
     */
    private $tenantSwitcher;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TenantSwitcher $tenantSwitcher)
    {
        parent::__construct();
        $this->tenantSwitcher = $tenantSwitcher;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->tenantSwitcher->switchGlobalTenant($tenant);

            $this->call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--database' => 'tenant'
            ]);
        }
    }
}
