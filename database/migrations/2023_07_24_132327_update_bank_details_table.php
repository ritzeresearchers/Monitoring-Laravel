<?php

use App\Models\Business;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBankDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_details', function (Blueprint $table) {
            $table->string('line1')->nullable();
            $table->string('line2')->nullable();
            $table->string('post_code')->nullable();
        });

        $businesses = Business::all();
        foreach ($businesses as $business) {
            $business->bankDetail()->update([
                'line1'     => $business->line1,
                'line2'     => $business->line2,
                'post_code' => $business->post_code,
            ]);
        }
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
