<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearOldCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::delete('DELETE FROM business_work_category');
        DB::delete('DELETE FROM business_services');
        DB::delete('DELETE FROM work_categories');
        DB::delete('DELETE FROM services');
        DB::delete('DELETE FROM jobs');
    }
}
