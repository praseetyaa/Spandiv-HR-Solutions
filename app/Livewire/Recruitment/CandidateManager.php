<?php

namespace App\Livewire\Recruitment;

use App\Models\Candidate;
use App\Models\JobPosting;
use Livewire\Component;

class CandidateManager extends Component
{
    public string $search = '';
    public string $stageFilter = '';
    public ?int $jobFilter = null;
    public ?int $selectedId = null;

    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $jobId = null;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $source = '';
    public string $notes = '';

    public function getCandidatesProperty()
    {
        return Candidate::with(['jobPosting.department'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->stageFilter, fn ($q) => $q->where('stage', $this->stageFilter))
            ->when($this->jobFilter, fn ($q) => $q->where('job_id', $this->jobFilter))
            ->latest()->get();
    }

    public function getJobsProperty() { return JobPosting::orderByDesc('created_at')->get(); }

    public function getStatsProperty()
    {
        return [
            'applied'   => Candidate::where('stage', 'applied')->count(),
            'screening' => Candidate::where('stage', 'screening')->count(),
            'interview' => Candidate::where('stage', 'interview')->count(),
            'offering'  => Candidate::where('stage', 'offering')->count(),
            'hired'     => Candidate::where('stage', 'hired')->count(),
        ];
    }

    public function selectCandidate(int $id): void { $this->selectedId = $this->selectedId === $id ? null : $id; }

    public function getSelectedProperty()
    {
        if (!$this->selectedId) return null;
        return Candidate::with(['jobPosting', 'interviews.interviewer'])->find($this->selectedId);
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'jobId', 'name', 'email', 'phone', 'source', 'notes']);
        if ($id) {
            $c = Candidate::findOrFail($id);
            $this->editingId = $c->id;
            $this->jobId = $c->job_id;
            $this->name = $c->name;
            $this->email = $c->email;
            $this->phone = $c->phone ?? '';
            $this->source = $c->source ?? '';
            $this->notes = $c->notes ?? '';
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'jobId' => 'required|exists:job_postings,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $data = ['job_id' => $this->jobId, 'name' => $this->name, 'email' => $this->email, 'phone' => $this->phone ?: null, 'source' => $this->source ?: null, 'notes' => $this->notes ?: null];
        if ($this->editingId) Candidate::findOrFail($this->editingId)->update($data);
        else Candidate::create(['tenant_id' => auth()->user()->tenant_id, 'stage' => 'applied', 'status' => 'active', ...$data]);
        $this->showForm = false;
    }

    public function moveStage(int $id, string $stage): void
    {
        $c = Candidate::findOrFail($id);
        $c->update(['stage' => $stage]);
    }

    public function delete(int $id): void
    {
        Candidate::findOrFail($id)->delete();
        if ($this->selectedId === $id) $this->selectedId = null;
    }

    public function render()
    {
        return view('livewire.recruitment.candidate-manager')
            ->layout('layouts.app', ['pageTitle' => 'Kandidat']);
    }
}
