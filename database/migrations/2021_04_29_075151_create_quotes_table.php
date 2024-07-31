<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->enum('rate_type', array_keys(config('constants.rateType')))->nullable();
            $table->string('currency')->nullable();
            $table->double('cost')->nullable();
            $table->dateTime('accepted_deadline')->nullable();
            $table->string('comments')->nullable();
            $table->boolean('is_accepted')->nullable()->default(null);
            $table->boolean('is_cancelled')->default(false);
            $table->enum('status', array_keys(config('constants.quoteStatus')))->default(config('constants.quoteStatus.pending'));
            $table->foreignId('job_id')->references('id')->on('jobs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('business_id')->references('id')->on('businesses');
            $table->foreignId('lead_id')->references('id')->on('leads')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('quotes');
    }
}
