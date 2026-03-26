<?php

namespace Database\Seeders;

use App\Services\TaskSchedulerService;
use Illuminate\Database\Seeder;

class ScheduledTaskSeeder extends Seeder
{
    public function run(): void
    {
        (new TaskSchedulerService())->seedDefaults();
    }
}
