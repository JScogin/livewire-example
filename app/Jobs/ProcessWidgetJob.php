<?php

namespace App\Jobs;

use App\Models\Widget;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWidgetJob implements ShouldQueue
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
    public $timeout = 60;

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
            // Calculate processing statistics
            $processingData = $this->calculateProcessingData();

            // Update widget metadata with processing results
            $metadata = $this->widget->metadata ?? [];
            $metadata['processing'] = [
                'processed_at' => now()->toIso8601String(),
                'total_value' => $processingData['total_value'],
                'is_high_value' => $processingData['is_high_value'],
                'processing_version' => 1,
            ];

            // Update widget
            $this->widget->update([
                'metadata' => $metadata,
                'processed_at' => now(),
            ]);

            Log::info("Widget {$this->widget->id} processed successfully");
        } catch (\Exception $e) {
            Log::error("Failed to process widget {$this->widget->id}: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Calculate processing data for the widget.
     *
     * @return array<string, mixed>
     */
    private function calculateProcessingData(): array
    {
        $price = $this->widget->price ?? 0;
        $quantity = $this->widget->quantity ?? 0;
        $totalValue = $price * $quantity;

        return [
            'total_value' => $totalValue,
            'is_high_value' => $totalValue > 1000,
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Widget processing job failed for widget {$this->widget->id}: " . $exception->getMessage());
    }
}
