<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadLocationsTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('lead_locations', function (Blueprint $table) {
            $table->id();
            $table->enum('location_type', array_keys(config('constants.locationTypes')));
            $table->double('radius', 8, 2)->nullable();
            $table->double('longitude', 8, 5)->nullable();
            $table->double('latitude', 8, 5)->nullable();
            $table->foreignId('business_id')->references('id')->on('businesses');
            $table->foreignId('location_id')->nullable()->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_locations');
    }
}
