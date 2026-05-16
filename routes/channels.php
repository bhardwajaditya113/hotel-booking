<?php

use Illuminate\Support\Facades\Broadcast;

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

/** Host-only: inbound reservation signals for property owners. */
Broadcast::channel('host.{id}', function ($user, $id) {
    return $user !== null && (int) $user->id === (int) $id;
});

/** Shared by guest, host (same web guard), and admin — any authenticated session may subscribe. */
Broadcast::channel('portal-sync', function ($user) {
    return $user !== null;
});
