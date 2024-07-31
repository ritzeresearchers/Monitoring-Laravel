<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->enum('message_type', array_keys(config('constants.messageTypes')));
            $table->text('text')->nullable();
            $table->text('media_link')->nullable();
            $table->text('media_name')->nullable();
            $table->enum('start_project_option_result', ['accepted', 'declined'])->nullable();
            $table->enum('bargain_cost_estimate_result', ['accepted', 'declined'])->nullable();
            $table->dateTime('adjustment_datetime')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('sender_business_id')->nullable();
            $table->foreignId('thread_id')->references('id')->on('message_threads')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
