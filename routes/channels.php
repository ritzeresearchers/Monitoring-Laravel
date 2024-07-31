<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('threadId.{threadId}', function ($user, $threadId) {
    if ( Auth::check()){
        return ['id' => $user->id, 'name' => $user->name];
    }
});

Broadcast::channel('new.message.notification.{userId}', function ($user, $threadId) {
    if (Auth::check()) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});

Broadcast::channel('new.job.lead.notification.{businessId}', function ($user, $threadId) {
    if (Auth::check()) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});