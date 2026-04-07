<?php

namespace App\Livewire\Recruitment;

use App\Models\Interview;
use App\Models\Candidate;
use Livewire\Component;
use Livewire\WithPagination;

class InterviewScheduler extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;
    public int $candidate_id = 0;
    public string $interview_type = 'hr';
    public string $scheduled_at = '';
    public string $location = '';
    public string $interviewer_name = '';
    public string $notes = '';
    public string $filterStatus = '';

    protected array $rules = [
        'candidate_id'     => 'required|exists:candidates,id',
        'interview_type'   => 'required|in:hr,technical,user,final',
        'scheduled_at'     => 'required|date|after:now',
        'location'         => 'nullable|string|max:255',
        'interviewer_name' => 'required|string|max:255',
        'notes'            => 'nullable|string|max:1000',
    ];

    public function save(): void
    {
        $this->validate();

        Interview::updateOrCreate(
            ['id' => $this->editingId],
            [
                'tenant_id'        => auth()->user()->tenant_id,
                'candidate_id'     => $this->candidate_id,
                'interview_type'   => $this->interview_type,
                'scheduled_at'     => $this->scheduled_at,
                'location'         => $this->location,
                'interviewer_name' => $this->interviewer_name,
                'notes'            => $this->notes,
                'status'           => 'scheduled',
            ]
        );

        $this->reset(['showModal', 'editingId', 'candidate_id', 'interview_type', 'scheduled_at', 'location', 'interviewer_name', 'notes']);
    }

    public function edit(int $id): void
    {
        $interview = Interview::findOrFail($id);
        $this->editingId        = $interview->id;
        $this->candidate_id     = $interview->candidate_id;
        $this->interview_type   = $interview->interview_type;
        $this->scheduled_at     = $interview->scheduled_at->format('Y-m-d\TH:i');
        $this->location         = $interview->location ?? '';
        $this->interviewer_name = $interview->interviewer_name;
        $this->notes            = $interview->notes ?? '';
        $this->showModal = true;
    }

    public function render()
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Interview::where('tenant_id', $tenantId)->with('candidate')->latest('scheduled_at');

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $candidates = Candidate::where('tenant_id', $tenantId)->whereIn('stage', ['screening', 'interview'])->get();

        return view('livewire.recruitment.interview-scheduler', [
            'interviews' => $query->paginate(15),
            'candidates' => $candidates,
        ]);
    }
}
