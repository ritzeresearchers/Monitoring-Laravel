<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('hired_business_id')->nullable();
            $table->dateTime('hired_datetime')->nullable();
            $table->dateTime('start_datetime')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('other_details')->nullable();
            $table->enum('job_type', array_keys(config('constants.jobType')));
            $table->enum('target_job_done', array_keys(config('constants.targetJobDone')));
            $table->dateTime('target_completion_datetime')->nullable();
            $table->enum('status', array_keys(config('constants.jobStatus')));
            $table->foreignId('poster_id')->nullable()->references('id')->on('users');
            $table->foreignId('category_id')->references('id')->on('work_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('jobs');
    }
}
