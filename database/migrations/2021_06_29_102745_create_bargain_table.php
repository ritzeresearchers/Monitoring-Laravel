<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBargainTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('bargain', function (Blueprint $table) {
            $table->id();
            $table->enum('rate_type', array_keys(config('constants.rateType')))->nullable();
            $table->double('cost')->default(0);
            $table->enum('status', array_keys(config('constants.bargainStatus')))->default(config('constants.bargainStatus.pending'));
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
            $table->foreignId('business_id')->nullable()->references('id')->on('businesses');
            $table->foreignId('message_id')->references('id')->on('messages')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('quote_id')->references('id')->on('quotes')->onDelete('cascade');
            $table->foreignId('job_id')->references('id')->on('jobs')->onDelete('cascade');
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
        Schema::dropIfExists('bargain');
    }
}
