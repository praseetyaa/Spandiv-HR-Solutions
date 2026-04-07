<?php

namespace App\Livewire\Recruitment;

use App\Models\Candidate;
use App\Models\Department;
use App\Models\JobPosting;
use App\Models\JobPosition;
use Livewire\Component;

class JobPostingManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public ?int $selectedId = null;

    public bool $showForm = false;
    public ?int $editingId = null;
    public string $title = '';
    public ?int $departmentId = null;
    public ?int $positionId = null;
    public string $description = '';
    public string $requirements = '';
    public string $employmentType = 'permanent';
    public ?float $salaryMin = null;
    public ?float $salaryMax = null;
    public int $openings = 1;
    public string $status = 'draft';
    public ?string $closeDate = null;

    public function getPostingsProperty()
    {
        return JobPosting::with(['department', 'position'])
            ->withCount('candidates')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()->get();
    }

    public function getDepartmentsProperty() { return Department::active()->orderBy('name')->get(); }
    public function getPositionsProperty() { return JobPosition::active()->orderBy('title')->get(); }

    public function getStatsProperty()
    {
        return [
            'total'     => JobPosting::count(),
            'published' => JobPosting::where('status', 'published')->count(),
            'closed'    => JobPosting::where('status', 'closed')->count(),
            'candidates'=> Candidate::count(),
        ];
    }

    public function selectPosting(int $id): void { $this->selectedId = $this->selectedId === $id ? null : $id; }

    public function getSelectedProperty()
    {
        if (!$this->selectedId) return null;
        return JobPosting::with(['department', 'position', 'candidates'])->find($this->selectedId);
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'title', 'departmentId', 'positionId', 'description', 'requirements', 'employmentType', 'salaryMin', 'salaryMax', 'openings', 'status', 'closeDate']);
        $this->employmentType = 'permanent';
        $this->openings = 1;
        $this->status = 'draft';
        if ($id) {
            $j = JobPosting::findOrFail($id);
            $this->editingId = $j->id;
            $this->title = $j->title;
            $this->departmentId = $j->department_id;
            $this->positionId = $j->position_id;
            $this->description = $j->description;
            $this->requirements = $j->requirements;
            $this->employmentType = $j->employment_type;
            $this->salaryMin = $j->salary_min;
            $this->salaryMax = $j->salary_max;
            $this->openings = $j->openings;
            $this->status = $j->status;
            $this->closeDate = $j->close_date?->format('Y-m-d');
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'departmentId' => 'required|exists:departments,id',
            'positionId' => 'required|exists:job_positions,id',
            'description' => 'required|string',
            'requirements' => 'required|string',
        ]);

        $data = [
            'title' => $this->title, 'department_id' => $this->departmentId,
            'position_id' => $this->positionId, 'description' => $this->description,
            'requirements' => $this->requirements, 'employment_type' => $this->employmentType,
            'salary_min' => $this->salaryMin, 'salary_max' => $this->salaryMax,
            'openings' => $this->openings, 'status' => $this->status,
            'close_date' => $this->closeDate ?: null,
        ];

        if ($this->editingId) {
            JobPosting::findOrFail($this->editingId)->update($data);
        } else {
            JobPosting::create(['tenant_id' => auth()->user()->tenant_id, 'created_by' => auth()->id(), ...$data]);
        }
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        JobPosting::findOrFail($id)->delete();
        if ($this->selectedId === $id) $this->selectedId = null;
    }

    public function render()
    {
        return view('livewire.recruitment.job-posting-manager')
            ->layout('layouts.app', ['pageTitle' => 'Lowongan']);
    }
}
