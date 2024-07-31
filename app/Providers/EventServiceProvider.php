<?php

namespace App\Providers;

use App\Events\BusinessCanceledJob;
use App\Events\BusinessUserRegistered;
use App\Events\BusinessWasAcceptedForJob;
use App\Events\CustomerUserRegistered;
use App\Events\GuestMessageSubmitted;
use App\Events\CustomerCanceledJob;
use App\Events\JobLeadPosted;
use App\Events\JobPosted;
use App\Events\NewMessageSent;
use App\Events\QuoteAccepted;
use App\Events\RequestSecurityCode;
use App\Events\ResendVerificationLink;
use App\Events\UpdateEmail;
use App\Events\UpdatePhoneNumber;
use App\Events\UserRegistered;
use App\Events\UserVerified;
use App\Listeners\CreateBusinessLeads;
use App\Listeners\EmailGuestMessageToAdmin;
use App\Listeners\EmailSecurityCode;
use App\Listeners\NotifyLeftQuotes;
use App\Listeners\PostPendingJob;
use App\Listeners\SendUserEmailVerification;
use App\Listeners\SendEmailVerification;
use App\Listeners\SendExternalNotification;
use App\Listeners\SendNewJobLeadNotification;
use App\Listeners\SendToOwnerEmailBusinessRegistered;
use App\Listeners\SendToOwnerEmailCustomerRegistered;
use App\Listeners\SendToOwnerEmailJobAccepted;
use App\Listeners\SendToOwnerEmailJobPosted;
use App\Listeners\SendUpdateEmailVerficationCode;
use App\Listeners\TextUpdatePhoneNumberVerificationCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Psy\ExecutionLoop\Listener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        UserRegistered::class => [
            SendEmailVerification::class
        ],

        BusinessUserRegistered::class => [
            SendUserEmailVerification::class,
            SendToOwnerEmailBusinessRegistered::class
        ],

        ResendVerificationLink::class => [
            SendUserEmailVerification::class
        ],

        CustomerUserRegistered::class => [
            SendUserEmailVerification::class,
            SendToOwnerEmailCustomerRegistered::class
        ],

        GuestMessageSubmitted::class => [
            EmailGuestMessageToAdmin::class
        ],

        RequestSecurityCode::class => [
            EmailSecurityCode::class
        ],

        JobPosted::class => [
            CreateBusinessLeads::class,
            SendToOwnerEmailJobPosted::class
        ],

        JobLeadPosted::class => [
            SendNewJobLeadNotification::class,
        ],

        BusinessWasAcceptedForJob::class => [
            SendToOwnerEmailJobAccepted::class
        ],

        NewMessageSent::class => [
            SendExternalNotification::class
        ],

        UpdateEmail::class => [
            SendUpdateEmailVerficationCode::class
        ],

        UpdatePhoneNumber::class => [
            TextUpdatePhoneNumberVerificationCode::class
        ],

        UserVerified::class => [
            PostPendingJob::class
        ],

        QuoteAccepted::class => [
            NotifyLeftQuotes::class,
        ],

        CustomerCanceledJob::class => [
            \App\Listeners\CustomerCanceledJob::class,
        ],

        BusinessCanceledJob::class => [
            \App\Listeners\BusinessCanceledJob::class,
        ],
    ];
}
