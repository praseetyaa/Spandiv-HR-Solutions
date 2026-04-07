<?php

namespace App\Services;

use App\Models\CandidateAnswer;
use App\Models\CandidateTestAssignment;
use App\Models\CandidateTestResult;
use App\Models\CandidateTestSession;
use App\Models\TestDimensionNorm;

class PsychTestScoringService
{
    public function scoreSession(CandidateTestSession $session): CandidateTestResult
    {
        $assignment = $session->assignment;
        $test       = $assignment->test;

        $answers = CandidateAnswer::where('session_id', $session->id)->get();

        // Calculate raw score
        $rawScore = 0;
        $dimensionScores = [];

        foreach ($answers as $answer) {
            if ($answer->selected_option_id) {
                $option = $answer->selectedOption;
                if ($option) {
                    $rawScore += (float) $option->score_value;

                    $dimKey = $option->dimension_key ?? $answer->question->dimension_key;
                    if ($dimKey) {
                        $dimensionScores[$dimKey] = ($dimensionScores[$dimKey] ?? 0) + (float) $option->score_value;
                    }
                }
            }
        }

        // Scale score (0-100)
        $maxPossible = $test->questions()->sum('points');
        $scaledScore = $maxPossible > 0 ? round(($rawScore / $maxPossible) * 100, 2) : 0;

        // Grade
        $grade = $this->determineGrade($scaledScore);

        // Dimension grades from norms
        $dimensionGrades = [];
        foreach ($dimensionScores as $dimKey => $score) {
            $norm = TestDimensionNorm::where('test_id', $test->id)
                ->where('dimension_key', $dimKey)
                ->where('score_min', '<=', $score)
                ->where('score_max', '>=', $score)
                ->first();

            $dimensionGrades[$dimKey] = $norm?->label ?? $this->determineGrade($score);
        }

        // Recommendation
        $recommendation = 'pending';
        if ($test->passing_score !== null) {
            $recommendation = $scaledScore >= $test->passing_score ? 'recommended' : 'not_recommended';
        }

        return CandidateTestResult::updateOrCreate(
            ['assignment_id' => $assignment->id],
            [
                'tenant_id'              => $assignment->tenant_id,
                'session_id'             => $session->id,
                'raw_score'              => $rawScore,
                'scaled_score'           => $scaledScore,
                'grade'                  => $grade,
                'dimension_scores'       => $dimensionScores,
                'dimension_grades'       => $dimensionGrades,
                'overall_recommendation' => $recommendation,
            ]
        );
    }

    protected function determineGrade(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default      => 'E',
        };
    }
}
