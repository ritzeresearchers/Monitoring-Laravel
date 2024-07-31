<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifiableEventsTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('notifiable_events', function (Blueprint $table) {
            $table->id();
            $table->enum('event', array_keys(config('constants.notifiableEvents')));
            $table->boolean('is_enabled')->default(false);
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifiable_events');
    }
}
