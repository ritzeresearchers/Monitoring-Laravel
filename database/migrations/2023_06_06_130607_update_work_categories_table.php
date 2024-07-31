<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateWorkCategoriesTable extends Migration
{
    public const CATEGORIES = [
        'Electrician',
        'Plumber',
        'Handyperson',
        'Roofer',
        'Builder',
        'Painter And Decorator',
        'Removal Services',
        'Cleaner'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_categories', function (Blueprint $table) {
            $table->boolean('is_featured')->nullable();
            $table->smallInteger('order')->nullable();
        });

        foreach (self::CATEGORIES as $order => $categoryName) {
            DB::table('work_categories')
                ->where('name', '=', $categoryName)
                ->update([
                    'order'       => $order,
                    'is_featured' => true,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
