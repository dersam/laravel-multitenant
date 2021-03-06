<?php

namespace Dersam\Multitenant\Commands;

use Dersam\Multitenant\Tenant;
use Dersam\Multitenant\TenantSwitcher;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;

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
    protected $description = 'Run all tenant migrations.';
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

        if ($tenants->count() === 0) {
            $this->error('No tenants found in tenant table.');
        }

        foreach ($tenants as $tenant) {
            $this->tenantSwitcher->switchGlobalTenant($tenant);

            $database = Config::get('database.connections.tenant.database');
            $message = "Migrating {$tenant->name} [{$database}]";
            $this->info($message);

            try {
                $this->call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--database' => 'tenant'
                ]);
            } catch (QueryException $e) {
                $this->error("Error migrating [{$database}] Skipping.");
                $this->error($e->getMessage());
            }
        }
    }
}
