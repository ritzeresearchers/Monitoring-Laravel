<?php

use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Events\CustomerUserRegistered;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Api\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', function () {
    return view('login');
});
Route::post('/login', function (Request $request) {
    $input = $request->only('email', 'password');
    if (Auth::attempt($input)) {
        $user = Auth::user();
        if ($user->subscribed('test')) {
            return redirect()->route('welcome');
        }
        return redirect()->route('stripe');
    } else {

        // authentication fail, back to login page with errors

        return Redirect::to('login')

            ->withErrors('Incorrect login details');
    }
})->name('login');
Route::middleware('auth.basic')->group(function () {
    Route::get('/payments', function () {
        return view('subscription.index');
    })->name('stripe');
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    Route::post('/payments', [SubscriptionController::class, 'processSubscription'])->name('stripe.post');
});
