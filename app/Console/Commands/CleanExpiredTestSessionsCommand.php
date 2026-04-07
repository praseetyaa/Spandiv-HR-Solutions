<?php
namespace App\Console\Commands;

use App\Models\CandidateTestSession;
use Illuminate\Console\Command;

class CleanExpiredTestSessionsCommand extends Command
{
    protected $signature = 'hr:clean-expired-sessions';
    protected $description = 'Mark expired psych test sessions as timed out';

    public function handle(): int
    {
        $expiryHours = config('hr.psych_test.token_expiry_hours', 72);

        $expired = CandidateTestSession::where('is_completed', false)
            ->where('started_at', '<', now()->subHours($expiryHours))
            ->whereNull('finished_at')
            ->get();

        foreach ($expired as $session) {
            $session->update([
                'is_completed' => true,
                'finished_at'  => now(),
                'is_timed_out' => true,
            ]);

            $session->assignment->update(['status' => 'expired']);
        }

        $this->info("Cleaned {$expired->count()} expired test sessions.");
        return self::SUCCESS;
    }
}
