<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTrackingEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    protected $eventType;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(Model $model, string $eventType, array $data)
    {
        $this->model = $model;
        $this->eventType = $eventType;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Create tracking event
            $this->model->trackingEvents()->create($this->data);

            // Update counter columns directly
            $now = Carbon::now();
            $updateData = [];
            $columnToIncrement = null;

            switch ($this->eventType) {
                case 'visit':
                    $columnToIncrement = 'visits_count';
                    $updateData['last_visited_at'] = $now;
                    break;
                case 'view':
                    $columnToIncrement = 'views_count';
                    $updateData['last_viewed_at'] = $now;
                    break;
                case 'show':
                    $columnToIncrement = 'shows_count';
                    $updateData['last_shown_at'] = $now;
                    break;
                case 'order':
                    $columnToIncrement = 'orders_count';
                    $updateData['last_ordered_at'] = $now;
                    break;
                case 'whatsapp':
                case 'WhatsAppClick':
                    $columnToIncrement = 'whatsapp_count';
                    break;
                case 'call':
                case 'PhoneCall':
                    $columnToIncrement = 'calls_count';
                    break;
            }

            if ($columnToIncrement) {
                $this->model->increment($columnToIncrement);
            }

            if (! empty($updateData)) {
                $this->model->update($updateData);
            }

        } catch (\Exception $e) {
            Log::error('Tracking event failed: '.$e->getMessage());
        }
    }
}
