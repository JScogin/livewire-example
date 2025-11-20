<?php

namespace App\Jobs;

use App\Mail\WidgetFollowUpEmail;
use App\Models\Widget;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWidgetFollowUpEmailJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Widget $widget
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Check if email is provided in metadata
            $email = $this->getEmailFromMetadata();

            if (!$email) {
                Log::info("No email found in metadata for widget {$this->widget->id}, skipping follow-up email");
                return;
            }

            // Check if email was already sent
            if ($this->widget->email_sent_at) {
                Log::info("Follow-up email already sent for widget {$this->widget->id}");
                return;
            }

            // Send the email
            Mail::to($email)->send(new WidgetFollowUpEmail($this->widget));

            // Update widget to mark email as sent
            $this->widget->update([
                'email_sent_at' => now(),
            ]);

            Log::info("Follow-up email sent for widget {$this->widget->id} to {$email}");
        } catch (\Exception $e) {
            Log::error("Failed to send follow-up email for widget {$this->widget->id}: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Get email address from widget metadata.
     */
    private function getEmailFromMetadata(): ?string
    {
        $metadata = $this->widget->metadata ?? [];

        return $metadata['email'] ?? $metadata['contact_email'] ?? null;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Follow-up email job failed for widget {$this->widget->id}: " . $exception->getMessage());
    }
}
