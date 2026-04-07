<?php

namespace App\Livewire\Performance;

use App\Models\Employee;
use App\Models\PerformanceReview;
use App\Models\ReviewCycle;
use App\Models\User;
use Livewire\Component;

class ReviewCycleManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public ?int $selectedCycleId = null;

    // Cycle form
    public bool $showCycleForm = false;
    public ?int $editingCycleId = null;
    public string $cycleName = '';
    public string $cycleType = 'annual';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public bool $is360 = false;

    // Review form
    public bool $showReviewForm = false;
    public ?int $editingReviewId = null;
    public ?int $reviewEmployeeId = null;
    public ?int $reviewReviewerId = null;
    public string $reviewerType = 'manager';
    public string $reviewRating = '';
    public ?string $reviewScore = null;
    public string $reviewSummary = '';
    public string $reviewStatus = 'draft';

    protected $listeners = ['refreshCycles' => '$refresh'];

    // ── Computed ──
    public function getCyclesProperty()
    {
        return ReviewCycle::withCount(['reviews', 'goals'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();
    }

    public function getSelectedCycleProperty()
    {
        if (!$this->selectedCycleId) return null;
        return ReviewCycle::with(['reviews.employee', 'reviews.reviewer'])->find($this->selectedCycleId);
    }

    public function getEmployeesProperty()
    {
        return Employee::active()->orderBy('first_name')->get();
    }

    public function getUsersProperty()
    {
        return User::orderBy('name')->get();
    }

    public function getStatsProperty(): array
    {
        return [
            'total'     => ReviewCycle::count(),
            'active'    => ReviewCycle::where('status', 'active')->count(),
            'completed' => ReviewCycle::where('status', 'completed')->count(),
            'reviews'   => PerformanceReview::count(),
        ];
    }

    // ── Select ──
    public function selectCycle(int $id): void
    {
        $this->selectedCycleId = $id;
    }

    // ── Cycle CRUD ──
    public function openCycleForm(?int $id = null): void
    {
        $this->reset(['editingCycleId', 'cycleName', 'cycleType', 'startDate', 'endDate', 'is360']);
        $this->cycleType = 'annual';
        if ($id) {
            $c = ReviewCycle::findOrFail($id);
            $this->editingCycleId = $c->id;
            $this->cycleName = $c->name;
            $this->cycleType = $c->type;
            $this->startDate = $c->start_date?->format('Y-m-d');
            $this->endDate = $c->end_date?->format('Y-m-d');
            $this->is360 = $c->is_360;
        }
        $this->showCycleForm = true;
    }

    public function saveCycle(): void
    {
        $this->validate([
            'cycleName' => 'required|string|max:255',
            'cycleType' => 'required',
            'startDate' => 'required|date',
            'endDate'   => 'required|date|after_or_equal:startDate',
        ]);

        $data = [
            'name'       => $this->cycleName,
            'type'       => $this->cycleType,
            'start_date' => $this->startDate,
            'end_date'   => $this->endDate,
            'is_360'     => $this->is360,
        ];

        if ($this->editingCycleId) {
            ReviewCycle::findOrFail($this->editingCycleId)->update($data);
        } else {
            ReviewCycle::create([
                'tenant_id'  => auth()->user()->tenant_id,
                'created_by' => auth()->id(),
                'status'     => 'draft',
                ...$data,
            ]);
        }
        $this->showCycleForm = false;
    }

    public function updateCycleStatus(int $id, string $status): void
    {
        ReviewCycle::findOrFail($id)->update(['status' => $status]);
    }

    public function deleteCycle(int $id): void
    {
        ReviewCycle::findOrFail($id)->delete();
        if ($this->selectedCycleId === $id) $this->selectedCycleId = null;
    }

    // ── Review CRUD ──
    public function openReviewForm(?int $id = null): void
    {
        $this->reset(['editingReviewId', 'reviewEmployeeId', 'reviewReviewerId', 'reviewerType', 'reviewRating', 'reviewScore', 'reviewSummary', 'reviewStatus']);
        $this->reviewerType = 'manager';
        $this->reviewStatus = 'draft';
        if ($id) {
            $r = PerformanceReview::findOrFail($id);
            $this->editingReviewId = $r->id;
            $this->reviewEmployeeId = $r->employee_id;
            $this->reviewReviewerId = $r->reviewer_id;
            $this->reviewerType = $r->reviewer_type;
            $this->reviewRating = $r->rating ?? '';
            $this->reviewScore = $r->final_score;
            $this->reviewSummary = $r->summary ?? '';
            $this->reviewStatus = $r->status ?? 'draft';
        }
        $this->showReviewForm = true;
    }

    public function saveReview(): void
    {
        $this->validate([
            'reviewEmployeeId' => 'required|exists:employees,id',
            'reviewReviewerId' => 'required|exists:users,id',
            'reviewerType'     => 'required',
        ]);

        $data = [
            'cycle_id'      => $this->selectedCycleId,
            'employee_id'   => $this->reviewEmployeeId,
            'reviewer_id'   => $this->reviewReviewerId,
            'reviewer_type' => $this->reviewerType,
            'rating'        => $this->reviewRating ?: null,
            'final_score'   => $this->reviewScore ?: null,
            'summary'       => $this->reviewSummary ?: null,
            'status'        => $this->reviewStatus,
        ];

        if ($this->reviewStatus === 'submitted') {
            $data['submitted_at'] = now();
        }

        if ($this->editingReviewId) {
            PerformanceReview::findOrFail($this->editingReviewId)->update($data);
        } else {
            PerformanceReview::create([
                'tenant_id' => auth()->user()->tenant_id,
                ...$data,
            ]);
        }
        $this->showReviewForm = false;
    }

    public function deleteReview(int $id): void
    {
        PerformanceReview::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.performance.review-cycle-manager')
            ->layout('layouts.app', ['pageTitle' => 'Siklus Review']);
    }
}
