<?php

return [
    'appEnv' => env('APP_ENV', 'local'),
    'domain' => env('APP_URL'),
    'appUrl' => env('APP_URL'),
    'appName' => env('APP_NAME'),
    'adminEmail' => env('ADMIN_EMAIL', 'Thelocalpro2023@gmail.com'),
    'adminPwd' => env('ADMIN_PWD', 'ad2002min02'),
    'appBaseUrl' => env('APP_BASE_URL'),
    'noReplyEmail' => env('NO_REPLY_EMAIL'),
    'ownerEmail' => env('OWNER_EMAIL', 'Thelocalpro2023@gmail.com'),
    'customerServiceEmail' => env('CUSTOMER_SERVICE_EMAIL', 'customerservice@thelocalpro.co.uk'),
    'contactUsRecipient' => env('CONTACT_US_RECIPIENT'),
    'currency' => env('CURRENCY', 'GBP'),
    'webmaster' => 'admin',
    'targetJobDone' => [
        'immediate' => 'immediate',
        'flexible' => 'flexible',
        'specificDate' => 'specificDate'
    ],
    'assetsBaseUrl' => env('AWS_S3_ASSETS_BASE_URL'),
    'acBaseUrl' => env('AC_BASE_URL'),
    'acPostUrl' => env('AC_POST_URL'),
    'acClientCode' => env('AC_CLIENT_CODE'),
    'acApiKey' => env('AC_API_KEY'),
    'testBusinessEmail' => 'business.test@gmail.com',
    'testCustomerEmail' => 'customer.test@gmail.com',
    'changePasswordUrl' => env('APP_BASE_URL') . 'forgot-password/reset',

    'globalTextApiUrl' => env('TEXT_GLOBAL_API_URL'),
    'globalTextUserName' => env('TEXT_GLOBAL_USER_NAME'),
    'globalTextPassword' => env('TEXT_GLOBAL_PASSWORD'),

    'deletedAccount' => '###deleted###_',
    'DB_DATABASE' => env('DB_DATABASE', 'not working'),
    'impersonationBlockedApi' => [
        'api/message',
        'api/quotes',
    ],
    'webhook_url' => env('APP_URL') . 'webhook',
    'paginationLimit' => env('PAGINATION_LIMIT', 10),
];
