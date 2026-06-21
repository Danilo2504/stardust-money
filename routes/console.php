<?php

use Illuminate\Support\Facades\Schedule;

switch (env('APP_ENV')) {
    case 'local':
        //
        break;
    case 'production':
        //
        break;
    default:
        Schedule::command('app:trash-cleaner')->daily()->at('00:00');
}
