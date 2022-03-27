<?php

namespace App\Listeners;

use App\Events\UserLoginSuccess;
use App\Models\LoginHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class StoreUserLoginHistoryNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserLoginSuccess  $event
     * @return void
     */
    public function handle(UserLoginSuccess $event)
    {
        $userInfo = $event->user;

        // $saveHistory = LoginHistory::create([
        //     'name' => $userInfo->name,
        //     'email' => $userInfo->email
        // ]);
        // return $saveHistory;

        $loginHistory = new LoginHistory;
        $loginHistory->user_id = $userInfo->id;
        $loginHistory->name = $userInfo->name;
        $loginHistory->email = $userInfo->email;
        $loginHistory->save();
        return $loginHistory;
    }
}
