<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginTokenTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('login_token', function (Blueprint $table) {
            $table->id();
            $table->text('token');
            $table->foreignId('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('login_token');
    }
}
