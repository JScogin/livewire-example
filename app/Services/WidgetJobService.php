<?php

namespace App\Services;

use App\Jobs\GenerateDailyWidgetReportJob;
use App\Jobs\ProcessWidgetBatchJob;
use App\Jobs\ProcessWidgetJob;
use App\Jobs\SendWidgetFollowUpEmailJob;
use App\Models\Widget;

class WidgetJobService
{
    /**
     * Dispatch a job to process a widget.
     *
     * @param Widget $widget
     * @return void
     */
    public function dispatchProcessing(Widget $widget): void
    {
        ProcessWidgetJob::dispatch($widget);
    }

    /**
     * Dispatch a follow-up email job with 24-hour delay.
     *
     * @param Widget $widget
     * @return void
     */
    public function dispatchFollowUp(Widget $widget): void
    {
        SendWidgetFollowUpEmailJob::dispatch($widget)
            ->delay(now()->addHours(24));
    }

    /**
     * Dispatch a batch processing job.
     *
     * @param array<int>|null $widgetIds Optional array of widget IDs to process
     * @return void
     */
    public function dispatchBatch(?array $widgetIds = null): void
    {
        ProcessWidgetBatchJob::dispatch($widgetIds ?? []);
    }

    /**
     * Dispatch daily report generation job.
     *
     * @return void
     */
    public function dispatchDailyReport(): void
    {
        GenerateDailyWidgetReportJob::dispatch();
    }
}

