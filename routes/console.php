<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('bank:process-fd-maturity')->dailyAt('00:30');
Schedule::command('bank:process-loan-od-interest')->dailyAt('01:00');
Schedule::command('queue:work --stop-when-empty')->everyFiveMinutes();
