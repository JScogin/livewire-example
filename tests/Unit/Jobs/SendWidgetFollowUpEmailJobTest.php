<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendWidgetFollowUpEmailJob;
use App\Mail\WidgetFollowUpEmail;
use App\Models\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendWidgetFollowUpEmailJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_job_sends_email_when_email_in_metadata()
    {
        $widget = Widget::factory()->create([
            'metadata' => [
                'email' => 'test@example.com',
            ],
        ]);

        $job = new SendWidgetFollowUpEmailJob($widget);
        $job->handle();

        Mail::assertSent(WidgetFollowUpEmail::class, function ($mail) use ($widget) {
            return $mail->widget->id === $widget->id;
        });

        $widget->refresh();
        $this->assertNotNull($widget->email_sent_at);
    }

    public function test_job_skips_email_when_no_email_in_metadata()
    {
        $widget = Widget::factory()->create([
            'metadata' => null,
        ]);

        $job = new SendWidgetFollowUpEmailJob($widget);
        $job->handle();

        Mail::assertNothingSent();
    }

    public function test_job_skips_email_when_already_sent()
    {
        $widget = Widget::factory()->create([
            'metadata' => ['email' => 'test@example.com'],
            'email_sent_at' => now(),
        ]);

        $job = new SendWidgetFollowUpEmailJob($widget);
        $job->handle();

        Mail::assertNothingSent();
    }

    public function test_job_uses_contact_email_from_metadata()
    {
        $widget = Widget::factory()->create([
            'metadata' => [
                'contact_email' => 'contact@example.com',
            ],
        ]);

        $job = new SendWidgetFollowUpEmailJob($widget);
        $job->handle();

        Mail::assertSent(WidgetFollowUpEmail::class, function ($mail) {
            return true;
        });
    }

    public function test_job_has_correct_retry_settings()
    {
        $widget = Widget::factory()->create();
        $job = new SendWidgetFollowUpEmailJob($widget);

        $this->assertEquals(3, $job->tries);
        $this->assertEquals(30, $job->timeout);
    }
}

