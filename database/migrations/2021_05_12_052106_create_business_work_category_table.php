<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessWorkCategoryTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('business_work_category', function (Blueprint $table) {
            $table->foreignId('business_id')->references('id')->on('businesses');
            $table->foreignId('work_category_id')->references('id')->on('work_categories')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_work_category');
    }
}
