<?php

namespace App\Livewire\Compliance;

use App\Models\DisciplinaryRecord;
use App\Models\Employee;
use Livewire\Component;

class DisciplinaryManager extends Component
{
    public string $search = '';
    public string $typeFilter = '';
    public string $levelFilter = '';

    // Form
    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $employeeId = null;
    public string $type = 'warning';
    public string $level = 'verbal';
    public string $violation = '';
    public string $actionTaken = '';
    public ?string $incidentDate = null;
    public ?string $warningExpiresAt = null;

    public function getRecordsProperty()
    {
        return DisciplinaryRecord::with(['employee.department', 'issuedBy'])
            ->when($this->search, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%")))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->when($this->levelFilter, fn ($q) => $q->where('level', $this->levelFilter))
            ->latest('incident_date')
            ->get();
    }

    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function getStatsProperty(): array
    {
        return [
            'total' => DisciplinaryRecord::count(),
            'active_warnings' => DisciplinaryRecord::where('type', 'warning')
                ->where(fn ($q) => $q->whereNull('warning_expires_at')->orWhere('warning_expires_at', '>', now()))
                ->count(),
            'sp3' => DisciplinaryRecord::where('level', 'sp3')->count(),
        ];
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'employeeId', 'type', 'level', 'violation', 'actionTaken', 'incidentDate', 'warningExpiresAt']);
        $this->type = 'warning';
        $this->level = 'verbal';
        if ($id) {
            $r = DisciplinaryRecord::findOrFail($id);
            $this->editingId = $r->id;
            $this->employeeId = $r->employee_id;
            $this->type = $r->type;
            $this->level = $r->level;
            $this->violation = $r->violation;
            $this->actionTaken = $r->action_taken;
            $this->incidentDate = $r->incident_date->format('Y-m-d');
            $this->warningExpiresAt = $r->warning_expires_at?->format('Y-m-d');
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'type' => 'required',
            'level' => 'required',
            'violation' => 'required|string',
            'actionTaken' => 'required|string',
            'incidentDate' => 'required|date',
        ]);

        $data = [
            'employee_id' => $this->employeeId,
            'type' => $this->type,
            'level' => $this->level,
            'violation' => $this->violation,
            'action_taken' => $this->actionTaken,
            'incident_date' => $this->incidentDate,
            'warning_expires_at' => $this->warningExpiresAt ?: null,
        ];

        if ($this->editingId) {
            DisciplinaryRecord::findOrFail($this->editingId)->update($data);
        } else {
            DisciplinaryRecord::create([
                'tenant_id' => auth()->user()->tenant_id,
                'issued_by' => auth()->id(),
                ...$data,
            ]);
        }
        $this->showForm = false;
    }

    public function delete(int $id): void { DisciplinaryRecord::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.compliance.disciplinary-manager')
            ->layout('layouts.app', ['pageTitle' => 'Catatan Disipliner']);
    }
}
