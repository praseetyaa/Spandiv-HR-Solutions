<?php

namespace App\Livewire\PsychTest;

use App\Models\Candidate;
use App\Models\CandidateTestAssignment;
use App\Models\PsychTest;
use Illuminate\Support\Str;
use Livewire\Component;

class TestAssignmentManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public ?int $testFilter = null;

    // Assignment form
    public bool $showAssignForm = false;
    public ?int $assignCandidateId = null;
    public ?int $assignTestId = null;
    public ?string $assignDeadline = null;
    public int $assignMaxAttempts = 1;

    // ── Computed ──
    public function getAssignmentsProperty()
    {
        return CandidateTestAssignment::with(['candidate', 'test', 'assignedBy', 'sessions', 'result'])
            ->when($this->search, fn ($q) => $q->whereHas('candidate', fn ($cq) => $cq->where('name', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->testFilter, fn ($q) => $q->where('test_id', $this->testFilter))
            ->latest()
            ->get();
    }

    public function getTestsProperty()
    {
        return PsychTest::active()->orderBy('name')->get();
    }

    public function getCandidatesProperty()
    {
        return Candidate::orderBy('name')->get();
    }

    public function getStatsProperty(): array
    {
        $all = CandidateTestAssignment::query();
        return [
            'total'       => $all->count(),
            'pending'     => CandidateTestAssignment::where('status', 'pending')->count(),
            'in_progress' => CandidateTestAssignment::where('status', 'in_progress')->count(),
            'completed'   => CandidateTestAssignment::where('status', 'completed')->count(),
        ];
    }

    // ── CRUD ──
    public function openAssignForm(): void
    {
        $this->reset(['assignCandidateId', 'assignTestId', 'assignDeadline', 'assignMaxAttempts']);
        $this->assignMaxAttempts = 1;
        $this->assignDeadline = now()->addDays(7)->format('Y-m-d');
        $this->showAssignForm = true;
    }

    public function saveAssignment(): void
    {
        $this->validate([
            'assignCandidateId' => 'required|exists:candidates,id',
            'assignTestId'      => 'required|exists:psych_tests,id',
            'assignDeadline'    => 'required|date|after:today',
        ]);

        CandidateTestAssignment::create([
            'tenant_id'    => auth()->user()->tenant_id,
            'candidate_id' => $this->assignCandidateId,
            'test_id'      => $this->assignTestId,
            'assigned_by'  => auth()->id(),
            'access_token' => Str::random(32),
            'deadline_at'  => $this->assignDeadline,
            'max_attempts' => $this->assignMaxAttempts,
            'status'       => 'pending',
        ]);
        $this->showAssignForm = false;
    }

    public function updateStatus(int $id, string $status): void
    {
        CandidateTestAssignment::findOrFail($id)->update(['status' => $status]);
    }

    public function deleteAssignment(int $id): void
    {
        CandidateTestAssignment::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.psych-test.test-assignment-manager')
            ->layout('layouts.app', ['pageTitle' => 'Penugasan Tes']);
    }
}
