<?php

namespace App\Http\Controllers\PsychTest;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitAnswerRequest;
use App\Jobs\ScorePsychTestJob;
use App\Models\CandidateAnswer;
use App\Models\CandidateTestAssignment;
use App\Models\CandidateTestSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicTestController extends Controller
{
    public function start(string $token)
    {
        $assignment = CandidateTestAssignment::where('access_token', $token)
            ->where('status', 'pending')
            ->where('deadline_at', '>', now())
            ->with(['test.sections.questions.options', 'candidate'])
            ->firstOrFail();

        $session = CandidateTestSession::firstOrCreate(
            ['assignment_id' => $assignment->id],
            [
                'started_at'        => now(),
                'ip_address'        => request()->ip(),
                'user_agent'        => request()->userAgent(),
                'tab_switch_count'  => 0,
                'is_completed'      => false,
            ]
        );

        $assignment->update(['status' => 'in_progress']);

        return view('psych-test.public.test', [
            'assignment' => $assignment,
            'session'    => $session,
            'test'       => $assignment->test,
            'candidate'  => $assignment->candidate,
        ]);
    }

    public function submitAnswer(SubmitAnswerRequest $request)
    {
        $validated = $request->validated();

        CandidateAnswer::updateOrCreate(
            ['session_id' => $validated['session_id'], 'question_id' => $validated['question_id']],
            [
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'answer_text'        => $validated['answer_text'] ?? null,
                'number_input'       => $validated['number_input'] ?? null,
                'time_spent_sec'     => $validated['time_spent_sec'] ?? 0,
                'answered_at'        => now(),
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    public function finish(Request $request, int $sessionId)
    {
        $session = CandidateTestSession::findOrFail($sessionId);

        $session->update([
            'finished_at'  => now(),
            'is_completed' => true,
        ]);

        ScorePsychTestJob::dispatch($session->id);

        return view('psych-test.public.complete', [
            'candidate' => $session->assignment->candidate,
        ]);
    }

    public function reportTabSwitch(Request $request, int $sessionId)
    {
        $session = CandidateTestSession::findOrFail($sessionId);
        $session->increment('tab_switch_count');

        $threshold = config('hr.psych_test.tab_switch_threshold', 3);

        if ($session->tab_switch_count >= $threshold) {
            $session->update([
                'is_flagged'   => true,
                'is_completed' => true,
                'finished_at'  => now(),
            ]);

            return response()->json(['status' => 'disqualified', 'message' => 'Sesi diakhiri karena pelanggaran.']);
        }

        return response()->json(['status' => 'warning', 'count' => $session->tab_switch_count, 'max' => $threshold]);
    }
}
