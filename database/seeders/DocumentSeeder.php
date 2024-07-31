<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        DocumentType::insert(config('documents'));
    }
}
