<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessNotifiableEvents extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('business_notifiable_events', function (Blueprint $table) {
            $table->id();
            $table->enum('event', array_keys(config('constants.notifiableEvents')));
            $table->boolean('is_enabled')->default(false);
            $table->foreignId('business_id')->nullable()->references('id')->on('businesses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_notifiable_events');
    }
}
