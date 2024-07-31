<?php

use App\Models\Blog;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\AuthenticateController;
use App\Http\Controllers\Api\LeadLocationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\WorkCategoryController;
use App\Http\Controllers\Api\Admin\ServiceController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\BusinessNotificationController;
use App\Http\Controllers\Api\Admin\JobController as AdminJobController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Api\Admin\BusinessController as AdminBusinessController;
use App\Http\Controllers\Api\Admin\WorkCategoryController as AdminCategoryController;

//Implementing Global Middleware
Route::middleware(['LogExecutionTime'])->group(function () {

Route::get( '/test', function() {
    
    return response("tested route");
});
// Authentication Routes
Route::get('email', function () {
    try {
       $m=Mail::raw('Sending emails with Mailgun and Laravel is easy as it seems!', function ($message) {
            $message->to('kiariepeter13@gmail.com');
        });
        dd($m,'Success');
    } catch (\Exception $e) {
        dd($e);
    }
    
});
Route::get('/subscription/packages', [SubscriptionController::class, 'retrievePlans']);
Route::post('/subscription/packages', [SubscriptionController::class, 'processSubscription']);

Route::get('/pass', function(){

    return bcrypt('1234567890');
});
Route::get('loginfailed', function () {
    return response()->json(['error' => 'unauthenticated']);
})->name('loginfailed');

Route::prefix('auth')->group(function () {
    Route::get('verify/{code}', [BusinessController::class, 'verifyEmail']);

    Route::post('business/register', [BusinessController::class, 'register']);
    Route::post('customer/register', [CustomerController::class, 'register']);

    Route::get('security-code/{email}/send', [AuthenticateController::class, 'sendSecurityCode']);
    Route::post('change-password', [AuthenticateController::class, 'changePassword']);

    Route::post('login', [AuthenticateController::class, 'login']);
    Route::post('logout', [AuthenticateController::class, 'logout']);

    /** 2FA Auth admin */
    Route::post('admin/credentials/check', [AdminAuthController::class, 'checkCredentials']);
    Route::post('admin/code/verify', [AdminAuthController::class, 'verifyCodeAndLogIn']);
    Route::get('admin/{admin}/code/resend', [AdminAuthController::class, 'resendVerificationCode']);

    Route::group([
        'middleware' => ['auth:api', 'cors', 'ImpersonationInterceptor'],
    ], static function () {
        Route::get('user', [AuthenticateController::class, 'getUser']);
        Route::post('update-password', [AuthenticateController::class, 'updatePassword']);
    });
});
//implementing the Log Middleware as a group

Route::middleware(['api', 'cors'])->namespace('App\Http\Controllers')->group(function () {
    Route::group([
        'middleware' => ['auth:api', 'cors', 'ImpersonationInterceptor','LogExecutionTime'],
    ], static function () {
        // Account
        Route::prefix('account')->group(function () {
            Route::delete('user/{user}', [UserController::class, 'deleteAccount']);
        });

        // Job and Business reviews
        Route::group(['middleware' => 'IsCustomer'], static function () {
            Route::get('business/{business}/posted-review', [ReviewController::class, 'getPostedJobReview']);
            Route::post('business/{business}/review', [ReviewController::class, 'postBusinessReview']);
        });

        Route::group(['middleware' => 'IsBusiness'], static function () {
            Route::prefix('business')->group(function () {
                Route::get('/', [BusinessController::class, 'getCurrentBusiness']);

                // Business: Leads
                Route::get('leads', [LeadController::class, 'getLeads']);
                Route::get('lead-locations', [LeadLocationController::class, 'getLocations']);
                Route::post('lead-locations', [LeadLocationController::class, 'postBusinessLocation']);

                Route::post('/', [BusinessController::class, 'updateBusiness']);
                Route::delete('document/{documentTypeId}', [BusinessController::class, 'deleteBusinessDocument']);

                Route::put('update-email', [BusinessController::class, 'updateEmail']);
                Route::post('confirm-update-email', [BusinessController::class, 'confirmUpdateEmail']);
                Route::put('update-mobile-number', [BusinessController::class, 'updateMobileNumber']);
                Route::post('confirm-update-mobile-number', [BusinessController::class, 'confirmUpdateMobileNumber']);
            });

            Route::post('lead/{lead}/report', [LeadController::class, 'postReport']);
            Route::post('/subs', [SubscriptionController::class, 'processSubscription']);
        });

        // Admin
        Route::group([
            'middleware' => 'IsAdmin',
            'prefix'     => 'admin',
        ], static function () {
            // Admin: Customer
            Route::get('customers', [AdminUserController::class, 'getCustomers']);
            Route::put('customer/{user}', [AdminUserController::class, 'updateCustomer']);
            Route::delete('user/{user}', [AdminUserController::class, 'deleteEntity']);

            // Admin: Business
            Route::get('businesses', [AdminBusinessController::class, 'getBusinesses']);
            Route::post('business/document/{businessDocument}/verify', [AdminBusinessController::class, 'verifyBusinessDocument']);
            Route::put('business/{business}', [AdminBusinessController::class, 'updateBusiness']);
            Route::get('resend-verification-code/{email}', [AdminBusinessController::class, 'resendVerificationCode']);

            // Admin: Job
            Route::get('jobs', [AdminJobController::class, 'getJobs']);
            Route::put('job/{job}', [AdminJobController::class, 'updateJob']);
            Route::delete('job/{job}', [AdminJobController::class, 'deleteJob']);

            // Admin: Work category
            Route::get('work-categories', [AdminCategoryController::class, 'getWorkCategories']);
            Route::post('work-category', [AdminCategoryController::class, 'postWorkCategory']);
            Route::put('work-category/{workCategory}', [AdminCategoryController::class, 'updateWorkCategory']);
            Route::delete('work-category/{workCategory}', [AdminCategoryController::class, 'deleteWorkCategory']);

            // Admin: Service
            Route::post('service', [ServiceController::class, 'postService']);
            Route::put('service/{service}', [ServiceController::class, 'updateService']);
            Route::delete('service/{service}', [ServiceController::class, 'deleteService']);

            // Admin: Review
            Route::get('reviews', [AdminReviewController::class, 'getReviews']);
            Route::delete('review/{review}', [AdminReviewController::class, 'deleteReview']);

            Route::post('impersonate/login', [AuthenticateController::class, 'impersonateLogin']);
        });

        Route::prefix('user')->group(function () {
            Route::post('/', [UserController::class, 'updateUser']);
            Route::put('update-email', [UserController::class, 'updateEmail']);
            Route::post('confirm-update-email', [UserController::class, 'confirmUpdateEmail']);
            Route::put('update-mobile-number', [UserController::class, 'updateMobileNumber']);
            Route::post('confirm-update-mobile-number', [UserController::class, 'confirmUpdateMobileNumber']);
        });

        // Jobs
        Route::get('jobs', [JobController::class, 'getJobs']);
        Route::post('job/{job}/update-status', [JobController::class, 'updateJobStatus']);
        Route::post('job', [JobController::class, 'postJob']);

        Route::get('job/{job}/responses', [QuoteController::class, 'getJobResponses'])->middleware('IsCustomer');
        Route::post('job/{job}/review', [ReviewController::class, 'postJobReview'])->middleware('IsCustomer');

        Route::get('job/{job}/review', [ReviewController::class, 'getJobReviews'])->middleware('IsBusiness');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'getNotifications']);
        Route::get('notifications/settings', [NotificationController::class, 'getNotificationSettings']);
        Route::get('business/notifications/settings', [NotificationController::class, 'getBusinessNotificationSettings']);
        Route::post('notifications/settings', [NotificationController::class, 'updateNotificationSettings']);
        Route::put('business/notifications/settings', [BusinessNotificationController::class, 'updateNotificationSettings'])->middleware('IsBusiness');

        // Quotes
        Route::get('quotes', [QuoteController::class, 'getQuotes']);
        Route::post('quote/{quote}/cancel', [QuoteController::class, 'cancelQuote']);
        Route::post('quote/{quote}/accept', [QuoteController::class, 'acceptQuote']);
        Route::post('quote', [QuoteController::class, 'postQuote']);

        Route::post('lead/{lead}/not-interested', [LeadController::class, 'notInterested']);
        Route::post('lead/{lead}/message', [LeadController::class, 'sendLeadMessage']);

        // Threads
        Route::get('message-thread/{messageThread}/messages', [MessageController::class, 'getThreadMessages']);
        Route::get('message-thread/{messageThread}/images', [MessageController::class, 'getThreadImages']);
        Route::post('message', [MessageController::class, 'postMessage']);
        Route::post('message/search', [MessageController::class, 'searchMessage']);
        Route::get('threads', [MessageController::class, 'getThreads']);
        Route::get('thread/{messageThread}', [MessageController::class, 'getThread']);
        Route::delete('thread/{messageThread}', [MessageController::class, 'deleteThread']);

        Route::post('login/back-as-admin', [AuthenticateController::class, 'loginBackAsAdmin']);
    });

    // Business
    Route::get('businesses/featured', [BusinessController::class, 'getFeatured']);
    Route::get('business/{business}', [BusinessController::class, 'getBusinessById']);
    Route::get('business/search', [BusinessController::class, 'searchBusiness']);

    // User: Email
    Route::get('user/is-email-registered/{email}', [UserController::class, 'isEmailRegistered']);
    Route::get('user/is-phone-registered/{phone}', [UserController::class, 'isPhoneRegistered']);

    // Work category
    Route::get('work-category', [WorkCategoryController::class, 'index']);
    Route::get('work-category/businesses', [WorkCategoryController::class, 'getWorkCategoryBusinesses']);
    Route::get('work-category/{workCategory}/services', [WorkCategoryController::class, 'getWorkCategoryServices']);

    Route::get('locations', [LocationController::class, 'index']);
    Route::post('leads/send', [LeadController::class, 'sendLeads']);
    Route::post('contact-us', [ContactUsController::class, 'sendInquiry']);

    Route::get('constants', function () {
        return json_encode(config('constants'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    });
    Route::get('blogs', function () {
        return Blog::orderBy('id', 'DESC')->paginate(20);
    });
});
});