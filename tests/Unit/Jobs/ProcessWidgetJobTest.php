<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessWidgetJob;
use App\Models\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ProcessWidgetJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_processes_widget_successfully()
    {
        $widget = Widget::factory()->create([
            'price' => 25.00,
            'quantity' => 10,
            'metadata' => null,
        ]);

        $job = new ProcessWidgetJob($widget);
        $job->handle();

        $widget->refresh();

        $this->assertNotNull($widget->processed_at);
        $this->assertIsArray($widget->metadata);
        $this->assertArrayHasKey('processing', $widget->metadata);
        $this->assertEquals(250.00, $widget->metadata['processing']['total_value']);
        $this->assertFalse($widget->metadata['processing']['is_high_value']);
    }

    public function test_job_calculates_high_value_correctly()
    {
        $widget = Widget::factory()->create([
            'price' => 150.00,
            'quantity' => 10,
        ]);

        $job = new ProcessWidgetJob($widget);
        $job->handle();

        $widget->refresh();

        $this->assertEquals(1500.00, $widget->metadata['processing']['total_value']);
        $this->assertTrue($widget->metadata['processing']['is_high_value']);
    }

    public function test_job_updates_existing_metadata()
    {
        $widget = Widget::factory()->create([
            'metadata' => [
                'color' => 'blue',
                'size' => 'large',
            ],
        ]);

        $job = new ProcessWidgetJob($widget);
        $job->handle();

        $widget->refresh();

        $this->assertEquals('blue', $widget->metadata['color']);
        $this->assertEquals('large', $widget->metadata['size']);
        $this->assertArrayHasKey('processing', $widget->metadata);
    }

    public function test_job_has_correct_retry_settings()
    {
        $widget = Widget::factory()->create();
        $job = new ProcessWidgetJob($widget);

        $this->assertEquals(3, $job->tries);
        $this->assertEquals(60, $job->timeout);
    }
}

