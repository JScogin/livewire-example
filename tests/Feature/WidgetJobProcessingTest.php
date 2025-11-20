<?php

namespace Tests\Feature;

use App\Jobs\GenerateDailyWidgetReportJob;
use App\Jobs\ProcessWidgetBatchJob;
use App\Jobs\ProcessWidgetJob;
use App\Jobs\SendWidgetFollowUpEmailJob;
use App\Models\Widget;
use App\Services\WidgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WidgetJobProcessingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_creating_widget_dispatches_processing_job()
    {
        $service = new WidgetService();

        $widget = $service->create([
            'name' => 'Test Widget',
            'price' => 29.99,
        ]);

        Queue::assertPushed(ProcessWidgetJob::class, function ($job) use ($widget) {
            return $job->widget->id === $widget->id;
        });
    }

    public function test_creating_widget_dispatches_follow_up_email_job()
    {
        $service = new WidgetService();

        $widget = $service->create([
            'name' => 'Test Widget',
            'metadata' => ['email' => 'test@example.com'],
        ]);

        Queue::assertPushed(SendWidgetFollowUpEmailJob::class, function ($job) use ($widget) {
            return $job->widget->id === $widget->id;
        });
    }

    public function test_updating_widget_dispatches_processing_job()
    {
        $service = new WidgetService();
        $widget = Widget::factory()->create();

        $service->update($widget->id, [
            'price' => 39.99,
        ]);

        Queue::assertPushed(ProcessWidgetJob::class);
    }

    public function test_batch_processing_job_can_be_dispatched()
    {
        Widget::factory()->count(5)->create();

        ProcessWidgetBatchJob::dispatch([]);

        Queue::assertPushed(ProcessWidgetBatchJob::class);
    }

    public function test_daily_report_job_can_be_dispatched()
    {
        GenerateDailyWidgetReportJob::dispatch();

        Queue::assertPushed(GenerateDailyWidgetReportJob::class);
    }

    public function test_follow_up_email_job_is_delayed()
    {
        $widget = Widget::factory()->create([
            'metadata' => ['email' => 'test@example.com'],
        ]);

        SendWidgetFollowUpEmailJob::dispatch($widget)
            ->delay(now()->addHours(24));

        Queue::assertPushed(SendWidgetFollowUpEmailJob::class, function ($job) {
            return $job->delay !== null;
        });
    }
}

