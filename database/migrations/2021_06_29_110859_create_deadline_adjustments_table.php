<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeadlineAdjustmentsTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('deadline_adjustments', function (Blueprint $table) {
            $table->id();
            $table->dateTime('adjustment_datetime');
            $table->enum('status', array_keys(config('constants.deadlineAdjustmentStatus')))->default(config('constants.deadlineAdjustmentStatus.pending'));
            $table->foreignId('job_id')->references('id')->on('jobs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('message_id')->references('id')->on('messages')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('quote_id')->references('id')->on('quotes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sender_id')->nullable()->default(null)->references('id')->on('users');
            $table->unsignedBigInteger('sender_business_id')->nullable()->default(null);
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
        Schema::dropIfExists('deadline_adjustments');
    }
}
