<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        $this->call([
            WorkCategorySeeder::class,
            LocationSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
