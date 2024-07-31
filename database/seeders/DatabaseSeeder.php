<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            WorkCategorySeeder::class,
            LocationSeeder::class,
            DocumentSeeder::class,
        ]);
    }
}
