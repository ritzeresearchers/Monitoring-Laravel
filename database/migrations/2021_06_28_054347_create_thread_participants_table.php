<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadParticipantsTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('thread_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->references('id')->on('message_threads')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('participant_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamp('last_read')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thread_participants');
    }
}
