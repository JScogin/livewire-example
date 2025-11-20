<?php

namespace App\Console\Commands;

use App\Jobs\GenerateDailyWidgetReportJob;
use Illuminate\Console\Command;

class GenerateDailyWidgetReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'widgets:generate-daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send the daily widget statistics report';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Dispatching daily widget report job...');

        GenerateDailyWidgetReportJob::dispatch();

        $this->info('Daily widget report job dispatched successfully.');

        return Command::SUCCESS;
    }
}
