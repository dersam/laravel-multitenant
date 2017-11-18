<?php

namespace Dersam\Multitenant\Commands;


use Dersam\Multitenant\Tenant;
use Dersam\Multitenant\TenantSwitcher;
use Illuminate\Console\Command;

class MigrateTenantRollback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multitenant:migrate:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback migrations for a specified tenant.';
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
            $this->call('migrate:rollback', [
                '--path' => 'database/migrations/tenant',
                '--database' => 'tenant'
            ]);
        }
    }
}
