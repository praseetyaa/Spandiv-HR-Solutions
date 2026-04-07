<?php
namespace App\Observers;

use App\Events\PsychTestCompleted;
use App\Models\CandidateTestSession;

class CandidateTestSessionObserver
{
    public function updated(CandidateTestSession $session): void
    {
        if ($session->wasChanged('is_completed') && $session->is_completed) {
            $session->assignment->update([
                'status'        => 'completed',
                'attempt_count' => $session->assignment->attempt_count + 1,
            ]);
            PsychTestCompleted::dispatch($session);
        }
    }
}
