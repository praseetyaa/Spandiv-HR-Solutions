<?php
namespace App\Listeners;

use App\Events\PsychTestAssigned;
use App\Services\NotificationDispatchService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPsychTestInvitation implements ShouldQueue
{
    public function __construct(private NotificationDispatchService $notif) {}

    public function handle(PsychTestAssigned $event): void
    {
        $a = $event->assignment->load(['candidate', 'test', 'assignedBy']);

        // Send email to candidate directly (not a User, so custom)
        if ($a->candidate?->email) {
            try {
                \Illuminate\Support\Facades\Mail::raw(
                    "Anda telah ditugaskan untuk mengikuti tes psikologi: {$a->test->name}.\n\n"
                    . "Silakan akses tes melalui tautan berikut sebelum {$a->deadline_at->format('d/m/Y')}.\n\n"
                    . "Token akses: {$a->access_token}",
                    fn ($msg) => $msg->to($a->candidate->email)->subject("Undangan Tes Psikologi - {$a->test->name}")
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("PsychTest invitation email failed: {$e->getMessage()}");
            }
        }

        $a->update(['notified_at' => now()]);
    }
}
