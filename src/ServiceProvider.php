<?php

namespace Dersam\Multitenant;

use Dersam\Multitenant\Commands\MigrateTenant;
use Dersam\Multitenant\Commands\MigrateTenantRollback;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->publishes([
            __DIR__.'/config/multitenant.php' => config_path('multitenant.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateTenant::class,
                MigrateTenantRollback::class,
            ]);
        }

        $this->app->singleton(TenantSwitcher::class, function ($app){
            return new TenantSwitcher($app);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/multitenant.php', 'multitenant'
        );
    }
}
