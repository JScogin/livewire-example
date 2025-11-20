<?php

namespace App\Jobs;

use App\Models\Widget;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWidgetBatchJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $widgetIds = []
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // If no widget IDs provided, process all unprocessed widgets
            if (empty($this->widgetIds)) {
                $widgets = Widget::whereNull('processed_at')
                    ->limit(50)
                    ->get();
            } else {
                $widgets = Widget::whereIn('id', $this->widgetIds)->get();
            }

            $processedCount = 0;

            foreach ($widgets as $widget) {
                // Dispatch individual processing job
                ProcessWidgetJob::dispatch($widget);
                $processedCount++;
            }

            Log::info("Batch processing job dispatched {$processedCount} widget processing jobs");
        } catch (\Exception $e) {
            Log::error("Failed to process widget batch: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Widget batch processing job failed: " . $exception->getMessage());
    }
}
