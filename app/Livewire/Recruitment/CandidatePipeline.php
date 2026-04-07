<?php

namespace App\Livewire\Recruitment;

use App\Models\Candidate;
use Livewire\Component;

class CandidatePipeline extends Component
{
    public ?int $jobId = null;
    public array $stages = ['applied', 'screening', 'interview', 'assessment', 'offering', 'hired', 'rejected'];

    public function moveCandidate(int $candidateId, string $newStage): void
    {
        $candidate = Candidate::where('tenant_id', auth()->user()->tenant_id)->findOrFail($candidateId);
        $candidate->update(['stage' => $newStage]);
    }

    public function render()
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Candidate::where('tenant_id', $tenantId)->with(['jobPosting']);

        if ($this->jobId) {
            $query->where('job_id', $this->jobId);
        }

        $candidates = $query->get();

        $pipeline = [];
        foreach ($this->stages as $stage) {
            $pipeline[$stage] = $candidates->where('stage', $stage)->values();
        }

        $jobs = \App\Models\JobPosting::where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->orderBy('title')
            ->get();

        return view('livewire.recruitment.candidate-pipeline', [
            'pipeline' => $pipeline,
            'jobs'     => $jobs,
        ]);
    }
}
