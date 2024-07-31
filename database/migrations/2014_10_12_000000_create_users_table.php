<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('security_code')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('mobile_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('mobile_number_verification_code')->nullable();
            $table->string('user_type');
            $table->timestamp('first_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('mobile_number_verified_at')->nullable();
            $table->string('password');
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('user_notifications_channels');
        Schema::dropIfExists('user_notifiable_events');
        Schema::dropIfExists('users');
    }
}
