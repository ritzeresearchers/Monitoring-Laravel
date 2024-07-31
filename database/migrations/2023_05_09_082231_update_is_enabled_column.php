<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateIsEnabledColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `notifiable_events` CHANGE COLUMN `is_enabled` `is_enabled` TINYINT NOT NULL DEFAULT 1;');
        DB::statement('ALTER TABLE `notification_channels` CHANGE COLUMN `is_enabled` `is_enabled` TINYINT NOT NULL DEFAULT 1;');
        DB::statement('ALTER TABLE `business_notification_channels` CHANGE COLUMN `is_enabled` `is_enabled` TINYINT NOT NULL DEFAULT 1;');
        DB::statement('ALTER TABLE `business_notification_channels` CHANGE COLUMN `is_enabled` `is_enabled` TINYINT NOT NULL DEFAULT 1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
