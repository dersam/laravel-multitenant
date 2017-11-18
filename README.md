# multitenant
Simple multitenant service provider for Laravel. Individual tenants will be given their own
database schema. It expects a separate "core" schema for managing application-wide settings.

This does not support multitenancy via owner columns, and never will.

## Installation
Install the package: 
```composer require dersam/laravel-multitenant```

Run the migration to create the `tenants` database in your core database.
```php artisan migrate```

Deploy the config.
```php artisan vendor:publish --provider="Dersam\Multitenant\ServiceProvider"```

Add a "dummy" config to `config/database.php` It should point to the database you want
to contain your tenant schemas, but do not specify a database. This will be set dynamically.
```$php
'connections' => [
    // The core database that contains global application tables.
    'core' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],
    // A "dummy" database that will be pointed to the correct tenant on the fly.
    'tenant' => [
        'driver'    => 'mysql',
        'host'      => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
    ],
],
```

## Usage
The service provider will be automatically registered - it can be injected with
`Dersam\Multitenant\TenantSwitcher`.  Retrieve your `Tenant` model, and pass that
to switch the `tenant` connection to your new database. In this example, it is
assumed that the User model has a `tenant_id` column, but this package leaves it
up to the developer to decide where this comes from.
```$php
$tenant = Tenant::find($user->tenant_id);
if ($tenant === null) {
    return redirect('/');
}
$this->tenantSwitcher->switchGlobalTenant($tenant);
```

You can specify that certain models always use the tenant connection with the
`Dersam\Multitenant\IsTenantModel` trait.
```$php
namespace App\Models\Tenant;

use Dersam\Multitenant\IsTenantModel;
...

class ExampleModel extends Model
{
    use IsTenantModel;
...
```

You can specify migrations that are run only on tenant databases by placing them in
the `database/migrations/tenant` directory. Migrations can be run on all tenants
with `php artisan multitenant:migrate` and rolled back with
 `php artisan multitenant:migrate:rollback`. These commands will read the `tenants` table
 and automatically discover the relevant tenant databases. Each tenant maintains its
 own migration table.