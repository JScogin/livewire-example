<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GenerateDailyWidgetReportJob;
use App\Mail\WidgetDailyReport;
use App\Models\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GenerateDailyWidgetReportJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_job_generates_and_sends_report()
    {
        // Create some test data
        Widget::factory()->count(5)->create();
        Widget::factory()->active()->count(3)->create();
        Widget::factory()->inactive()->count(2)->create();

        $job = new GenerateDailyWidgetReportJob();
        $job->handle();

        Mail::assertSent(WidgetDailyReport::class, function ($mail) {
            $reportData = $mail->reportData;
            $this->assertArrayHasKey('report_date', $reportData);
            $this->assertArrayHasKey('today', $reportData);
            $this->assertArrayHasKey('totals', $reportData);
            return true;
        });
    }

    public function test_job_calculates_correct_statistics()
    {
        Widget::factory()->create(['created_at' => now()]);
        Widget::factory()->create(['created_at' => now()->subDay()]);

        $job = new GenerateDailyWidgetReportJob();
        $job->handle();

        Mail::assertSent(WidgetDailyReport::class, function ($mail) {
            $reportData = $mail->reportData;
            $this->assertGreaterThanOrEqual(1, $reportData['today']['created']);
            return true;
        });
    }

    public function test_job_has_correct_retry_settings()
    {
        $job = new GenerateDailyWidgetReportJob();

        $this->assertEquals(2, $job->tries);
        $this->assertEquals(120, $job->timeout);
    }
}

