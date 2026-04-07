<?php
namespace App\Jobs;

use App\Models\Notification;
use App\Services\NotificationDispatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendScheduledNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $pending = Notification::whereNull('sent_at')
            ->where('scheduled_at', '<=', now())
            ->limit(100)
            ->get();

        foreach ($pending as $notif) {
            $notif->update(['sent_at' => now()]);
        }
    }
}
