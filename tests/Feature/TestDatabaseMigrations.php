<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;

trait TestDatabaseMigrations
{
    use CanConfigureMigrationCommands;

    private static bool $migrated = false;

    /**
     * @return void
     */
    public function runDatabaseMigrations()
    {
        if (!self::$migrated) {
            $this->artisan('db:wipe');

            $this->artisan('migrate');

            $this->artisan('db:seed --class=TestDatabaseSeeder');
            self::$migrated = true;
        }
    }
}
