<?php

namespace App\Jobs;

use App\Mail\WidgetDailyReport;
use App\Models\Widget;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateDailyWidgetReportJob implements ShouldQueue
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
    public $timeout = 120;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $reportData = $this->generateReportData();

            // Get admin email from config or env
            $adminEmail = config('mail.admin_email', env('MAIL_TARGET', 'admin@example.com'));

            // Send the report email
            Mail::to($adminEmail)->send(new WidgetDailyReport($reportData));

            Log::info("Daily widget report generated and sent to {$adminEmail}", $reportData);
        } catch (\Exception $e) {
            Log::error("Failed to generate daily widget report: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Generate daily report data.
     *
     * @return array<string, mixed>
     */
    private function generateReportData(): array
    {
        $today = now()->startOfDay();
        $yesterday = $today->copy()->subDay();

        // Get statistics for today
        $createdToday = Widget::whereDate('created_at', $today)->count();
        $updatedToday = Widget::whereDate('updated_at', $today)
            ->whereColumn('updated_at', '!=', 'created_at')
            ->count();
        $deletedToday = Widget::onlyTrashed()
            ->whereDate('deleted_at', $today)
            ->count();

        // Get statistics for yesterday (for comparison)
        $createdYesterday = Widget::whereDate('created_at', $yesterday)->count();
        $updatedYesterday = Widget::whereDate('updated_at', $yesterday)
            ->whereColumn('updated_at', '!=', 'created_at')
            ->count();

        // Get total statistics
        $totalWidgets = Widget::count();
        $totalActive = Widget::active()->count();
        $totalInactive = Widget::inactive()->count();
        $totalArchived = Widget::where('status', 'archived')->count();

        // Get processing statistics
        $processedToday = Widget::whereDate('processed_at', $today)->count();
        $emailsSentToday = Widget::whereDate('email_sent_at', $today)->count();

        return [
            'report_date' => $today->toDateString(),
            'today' => [
                'created' => $createdToday,
                'updated' => $updatedToday,
                'deleted' => $deletedToday,
                'processed' => $processedToday,
                'emails_sent' => $emailsSentToday,
            ],
            'yesterday' => [
                'created' => $createdYesterday,
                'updated' => $updatedYesterday,
            ],
            'totals' => [
                'total' => $totalWidgets,
                'active' => $totalActive,
                'inactive' => $totalInactive,
                'archived' => $totalArchived,
            ],
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Daily widget report job failed: " . $exception->getMessage());
    }
}
