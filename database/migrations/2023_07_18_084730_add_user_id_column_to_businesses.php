<?php

use App\Models\Business;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUserIdColumnToBusinesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_login_at');
        });

        DB::statement('ALTER TABLE `businesses` ADD COLUMN `is_subscription_active` TINYINT NOT NULL DEFAULT 1;');

        Schema::dropIfExists('business_aps');
        Schema::dropIfExists('contract_return_payload');
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('payment_requests');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('invited_users');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('payment_return_payload');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('schedules');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            //
        });
    }
}
