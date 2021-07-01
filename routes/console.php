<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    //$this->comment(Inspiring::quote());

    $user = User::find(1);
//    $message = $user->messages()->create(['message' => '777']);

    $message = \App\Models\Message::create(['message' => '777', 'user_id' => $user->id]);

    event(new \App\Events\MessageSent($user, $message));

    echo '==OK!==';

})->purpose('Display an inspiring quote');
