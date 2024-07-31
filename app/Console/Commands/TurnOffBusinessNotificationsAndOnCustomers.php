<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;

class TurnOffBusinessNotificationsAndOnCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will off for all current businesses all their notifications. And will on for all customers. This action forced change data in DB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $this->turnOnCustomersNotifications();
            $this->turnOffBusinessNotifications();
            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            Log::error('Error', [
                'error'  => $throwable->getMessage(),
                'trace'  => $throwable->getTraceAsString()
            ]);

            throw $throwable;
        }

        return CommandAlias::SUCCESS;
    }

    protected function turnOnCustomersNotifications() {
        $customers = User::where('user_type', 'customer')->get();

        /** @var User $customer */
        foreach ($customers as $customer) {
            foreach (config('constants.notificationChannelTypes') as $channelType) {
                $customer->notificationChannels()->updateOrCreate(
                    ['channel' => $channelType],
                    ['is_enabled' => true]
                );
            }

            foreach (config('constants.notifiableEvents') as $event) {
                $customer->notifiableEvents()->updateOrCreate(
                    ['event' => $event],
                    ['is_enabled' => true]
                );
            }
        }
    }

    protected function turnOffBusinessNotifications() {
        $businesses = Business::get();

        /** @var Business $business */
        foreach ($businesses as $business) {
            foreach (config('constants.notificationChannelTypes') as $channelType) {
                $business->notificationChannels()->updateOrCreate(
                    ['channel' => $channelType],
                    ['is_enabled' => false]
                );
            }

            foreach (config('constants.businessNotifiableEvents') as $event) {
                $business->notifiableEvents()->updateOrCreate(
                    ['event' => $event],
                    ['is_enabled' => false]
                );
            }
        }
    }
}
