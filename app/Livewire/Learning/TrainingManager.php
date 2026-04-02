<?php

namespace App\Livewire\Learning;

use App\Models\Employee;
use App\Models\TrainingParticipant;
use App\Models\TrainingProgram;
use App\Models\TrainingSchedule;
use Livewire\Component;

class TrainingManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public array $expandedPrograms = [];

    // Program Form
    public bool $showProgramForm = false;
    public ?int $editingProgramId = null;
    public string $programName = '';
    public string $programDescription = '';
    public string $programCategory = '';
    public int $programMaxParticipants = 30;

    // Schedule Form
    public bool $showScheduleForm = false;
    public ?int $editingScheduleId = null;
    public ?int $scheduleProgramId = null;
    public ?string $scheduleStartDate = null;
    public ?string $scheduleEndDate = null;
    public string $scheduleLocation = '';
    public string $scheduleMode = 'offline';
    public string $scheduleMeetingUrl = '';
    public string $scheduleTrainerName = '';
    public int $scheduleSeats = 30;

    // Participant Form
    public bool $showParticipantForm = false;
    public ?int $participantScheduleId = null;
    public ?int $participantEmployeeId = null;

    public function getProgramsProperty()
    {
        return TrainingProgram::with(['schedules.participants.employee'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->get();
    }

    public function getEmployeesProperty()
    {
        return Employee::active()->orderBy('first_name')->get();
    }

    public function toggleProgram(int $id): void
    {
        if (in_array($id, $this->expandedPrograms)) {
            $this->expandedPrograms = array_values(array_diff($this->expandedPrograms, [$id]));
        } else {
            $this->expandedPrograms[] = $id;
        }
    }

    // ── Program CRUD ──
    public function openProgramForm(?int $id = null): void
    {
        $this->resetProgramForm();
        if ($id) {
            $p = TrainingProgram::findOrFail($id);
            $this->editingProgramId = $p->id;
            $this->programName = $p->name;
            $this->programDescription = $p->description ?? '';
            $this->programCategory = $p->category;
            $this->programMaxParticipants = $p->max_participants;
        }
        $this->showProgramForm = true;
    }

    public function saveProgram(): void
    {
        $this->validate([
            'programName'     => 'required|string|max:255',
            'programCategory' => 'required|string|max:100',
            'programMaxParticipants' => 'required|integer|min:1',
        ]);

        $data = [
            'name'             => $this->programName,
            'description'      => $this->programDescription ?: null,
            'category'         => $this->programCategory,
            'max_participants'  => $this->programMaxParticipants,
        ];

        if ($this->editingProgramId) {
            TrainingProgram::findOrFail($this->editingProgramId)->update($data);
        } else {
            $data['tenant_id'] = auth()->user()->tenant_id;
            TrainingProgram::create($data);
        }

        $this->showProgramForm = false;
        $this->resetProgramForm();
    }

    public function deleteProgram(int $id): void
    {
        TrainingProgram::findOrFail($id)->delete();
    }

    private function resetProgramForm(): void
    {
        $this->editingProgramId = null;
        $this->programName = '';
        $this->programDescription = '';
        $this->programCategory = '';
        $this->programMaxParticipants = 30;
    }

    // ── Schedule CRUD ──
    public function openScheduleForm(int $programId, ?int $scheduleId = null): void
    {
        $this->resetScheduleForm();
        $this->scheduleProgramId = $programId;

        if ($scheduleId) {
            $s = TrainingSchedule::findOrFail($scheduleId);
            $this->editingScheduleId = $s->id;
            $this->scheduleStartDate = $s->start_date->format('Y-m-d');
            $this->scheduleEndDate = $s->end_date->format('Y-m-d');
            $this->scheduleLocation = $s->location ?? '';
            $this->scheduleMode = $s->mode;
            $this->scheduleMeetingUrl = $s->meeting_url ?? '';
            $this->scheduleTrainerName = $s->trainer_name ?? '';
            $this->scheduleSeats = $s->available_seats;
        }
        $this->showScheduleForm = true;
    }

    public function saveSchedule(): void
    {
        $this->validate([
            'scheduleProgramId' => 'required|exists:training_programs,id',
            'scheduleStartDate' => 'required|date',
            'scheduleEndDate'   => 'required|date|after_or_equal:scheduleStartDate',
            'scheduleMode'      => 'required|in:offline,online,hybrid',
            'scheduleSeats'     => 'required|integer|min:1',
        ]);

        $data = [
            'program_id'      => $this->scheduleProgramId,
            'start_date'      => $this->scheduleStartDate,
            'end_date'        => $this->scheduleEndDate,
            'location'        => $this->scheduleLocation ?: null,
            'mode'            => $this->scheduleMode,
            'meeting_url'     => $this->scheduleMeetingUrl ?: null,
            'trainer_name'    => $this->scheduleTrainerName ?: null,
            'available_seats' => $this->scheduleSeats,
        ];

        if ($this->editingScheduleId) {
            TrainingSchedule::findOrFail($this->editingScheduleId)->update($data);
        } else {
            TrainingSchedule::create($data);
        }

        $this->showScheduleForm = false;
        $this->resetScheduleForm();
    }

    public function deleteSchedule(int $id): void
    {
        TrainingSchedule::findOrFail($id)->delete();
    }

    private function resetScheduleForm(): void
    {
        $this->editingScheduleId = null;
        $this->scheduleProgramId = null;
        $this->scheduleStartDate = null;
        $this->scheduleEndDate = null;
        $this->scheduleLocation = '';
        $this->scheduleMode = 'offline';
        $this->scheduleMeetingUrl = '';
        $this->scheduleTrainerName = '';
        $this->scheduleSeats = 30;
    }

    // ── Participant ──
    public function openParticipantForm(int $scheduleId): void
    {
        $this->participantScheduleId = $scheduleId;
        $this->participantEmployeeId = null;
        $this->showParticipantForm = true;
    }

    public function addParticipant(): void
    {
        $this->validate(['participantEmployeeId' => 'required|exists:employees,id']);

        TrainingParticipant::firstOrCreate([
            'schedule_id' => $this->participantScheduleId,
            'employee_id' => $this->participantEmployeeId,
        ], [
            'registered_by' => auth()->id(),
            'status'        => 'registered',
        ]);

        $this->showParticipantForm = false;
    }

    public function markAttended(int $id): void
    {
        TrainingParticipant::findOrFail($id)->update(['status' => 'attended', 'is_attended' => true]);
    }

    public function removeParticipant(int $id): void
    {
        TrainingParticipant::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.learning.training-manager')
            ->layout('layouts.app', ['pageTitle' => 'Training Program']);
    }
}
