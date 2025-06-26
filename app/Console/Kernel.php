<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ScrapeCategoryJob;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\ScrapeProduct::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new ScrapeCategoryJob('https://www.amazon.com/s?k=laptop'))->everyFifteenMinutes();
        $schedule->job(new ScrapeCategoryJob('https://www.jumia.com.eg/laptops/'))->everyFifteenMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
