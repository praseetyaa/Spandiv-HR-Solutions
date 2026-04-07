<?php
namespace App\Jobs;

use App\Models\CandidateTestSession;
use App\Services\PsychTestScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScorePsychTestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $sessionId) {}

    public function handle(PsychTestScoringService $scoring): void
    {
        $session = CandidateTestSession::findOrFail($this->sessionId);
        $scoring->scoreSession($session);
    }
}
