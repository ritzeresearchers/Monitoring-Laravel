<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use function config;

class AdminSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        $email = config('config.adminEmail');
        $pwd = config('config.adminPwd');


        User::create([
            'name'              => 'localpro admin',
            'first_name'        => 'localpro',
            'last_name'         => 'admin',
            'email'             => $email,
            'email_verified_at' => now(),
            'user_type'         => config('constants.accountType.admin'),
            'avatar'            => 'https://localpro-assets.s3.eu-west-2.amazonaws.com/avatars/review-thumb.9914fd8.png',
            'password'          => bcrypt($pwd),
        ]);
    }
}
