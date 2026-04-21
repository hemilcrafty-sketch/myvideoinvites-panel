<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);

        $this->loadOrganizedMigrations();
    }

    /**
     * Load migrations from organized subdirectories for the default connection.
     *
     * Structure: database/migrations/{database_name}/{table_name}/migration_file.php
     * (Both {database_name} and {table_name} folders are required — files directly under
     * database/migrations/{database_name}/ are NOT auto-loaded.)
     *
     * Paths under database/migrations/crafty_creator/ and database/migrations/crafty_vendor/
     * are excluded so `php artisan migrate` on the default DB does not register them.
     * Run those separately:
     * - `php artisan migrate:crafty-creator` (connection: crafty_creator_mysql)
     * - `php artisan migrate:crafty-vendor` (connection: crafty_vendor_mysql)
     */
    private function loadOrganizedMigrations(): void
    {
        $basePath = str_replace('\\', '/', database_path('migrations'));
        $migrationPaths = [];

        foreach (glob($basePath . '/*/*', GLOB_ONLYDIR) ?: [] as $path) {
            $normalizedPath = str_replace('\\', '/', $path);
            $relative = ltrim(substr($normalizedPath, strlen($basePath)), '/');
            $topSegment = explode('/', $relative, 2)[0] ?? '';

            if (in_array($topSegment, ['crafty_creator', 'crafty_vendor'], true)) {
                continue;
            }

            $migrationPaths[] = $normalizedPath;
        }

        if ($migrationPaths !== []) {
            $this->loadMigrationsFrom($migrationPaths);
        }
    }
}
