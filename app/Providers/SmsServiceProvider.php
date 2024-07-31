<?php

namespace App\Providers;

use App\Services\Contracts\SMSInterface;
use App\Services\TextGlobalSMSService;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SMSInterface::class, TextGlobalSMSService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
