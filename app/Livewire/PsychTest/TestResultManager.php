<?php

namespace App\Livewire\PsychTest;

use App\Models\CandidateTestResult;
use App\Models\PsychTest;
use Livewire\Component;

class TestResultManager extends Component
{
    public string $search = '';
    public string $recommendationFilter = '';
    public ?int $testFilter = null;

    // Review
    public bool $showReviewForm = false;
    public ?int $editingResultId = null;
    public string $reviewerNotes = '';
    public string $recommendation = 'pending';

    // Detail
    public ?int $selectedResultId = null;

    // ── Computed ──
    public function getResultsProperty()
    {
        return CandidateTestResult::with([
                'assignment.candidate',
                'assignment.test',
                'session',
                'reviewer',
            ])
            ->when($this->search, fn ($q) => $q->whereHas('assignment.candidate', fn ($cq) => $cq->where('name', 'like', "%{$this->search}%")))
            ->when($this->recommendationFilter, fn ($q) => $q->where('overall_recommendation', $this->recommendationFilter))
            ->when($this->testFilter, fn ($q) => $q->whereHas('assignment', fn ($aq) => $aq->where('test_id', $this->testFilter)))
            ->latest()
            ->get();
    }

    public function getSelectedResultProperty()
    {
        if (!$this->selectedResultId) return null;
        return CandidateTestResult::with(['assignment.candidate', 'assignment.test', 'session', 'reviewer'])->find($this->selectedResultId);
    }

    public function getTestsProperty()
    {
        return PsychTest::orderBy('name')->get();
    }

    public function getStatsProperty(): array
    {
        return [
            'total'               => CandidateTestResult::count(),
            'highly_recommended'  => CandidateTestResult::where('overall_recommendation', 'highly_recommended')->count(),
            'recommended'         => CandidateTestResult::where('overall_recommendation', 'recommended')->count(),
            'not_recommended'     => CandidateTestResult::where('overall_recommendation', 'not_recommended')->count(),
        ];
    }

    // ── Actions ──
    public function selectResult(int $id): void
    {
        $this->selectedResultId = $id;
    }

    public function openReviewForm(int $id): void
    {
        $result = CandidateTestResult::findOrFail($id);
        $this->editingResultId = $result->id;
        $this->reviewerNotes = $result->reviewer_notes ?? '';
        $this->recommendation = $result->overall_recommendation ?? 'pending';
        $this->showReviewForm = true;
    }

    public function saveReview(): void
    {
        $this->validate([
            'recommendation' => 'required',
        ]);

        CandidateTestResult::findOrFail($this->editingResultId)->update([
            'reviewer_notes'         => $this->reviewerNotes ?: null,
            'overall_recommendation' => $this->recommendation,
            'reviewed_by'            => auth()->id(),
            'reviewed_at'            => now(),
        ]);
        $this->showReviewForm = false;
    }

    public function publishResult(int $id): void
    {
        CandidateTestResult::findOrFail($id)->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    public function render()
    {
        return view('livewire.psych-test.test-result-manager')
            ->layout('layouts.app', ['pageTitle' => 'Hasil Tes']);
    }
}
