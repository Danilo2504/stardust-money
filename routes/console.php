<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:trash-cleaner')->daily()->at('00:00');
Schedule::command('app:generate-recurring-expenses')->daily()->at('00:00');
