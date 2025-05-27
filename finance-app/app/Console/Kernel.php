<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Lista de comandos personalizados.
     */
    protected $commands = [
      \App\Console\Commands\ProcessCreditCardAutoDebit::class,
      \App\Console\Commands\ProcessAutoDebits::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
      $schedule->command('debitos:processar')->dailyAt('17:00');
      $schedule->command('credit-cards:auto-debit')->dailyAt('03:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
      $this->load(__DIR__.'/Commands');
      require base_path('routes/console.php');
    }
}
