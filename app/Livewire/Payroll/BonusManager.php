<?php

namespace App\Livewire\Payroll;

use App\Models\BonusScheme;
use App\Models\Employee;
use App\Models\EmployeeBonus;
use Livewire\Component;

class BonusManager extends Component
{
    public string $tab = 'distributions'; // distributions | schemes
    public string $search = '';
    public string $statusFilter = '';

    // Distribution form
    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $employeeId = null;
    public ?int $schemeId = null;
    public float $amount = 0;
    public string $notes = '';

    // Scheme form
    public bool $showSchemeForm = false;
    public ?int $editingSchemeId = null;
    public string $schemeName = '';
    public string $schemeType = 'fixed';
    public ?float $percentage = null;
    public ?float $fixedAmount = null;
    public string $period = 'annually';

    public function getDistributionsProperty()
    {
        return EmployeeBonus::with(['employee.department', 'scheme'])
            ->when($this->search, fn ($q) => $q->whereHas('employee', fn ($e) =>
                $e->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%")
            ))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()->get();
    }

    public function getSchemesProperty() { return BonusScheme::withCount('employeeBonuses')->orderBy('name')->get(); }
    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'employeeId', 'schemeId', 'amount', 'notes']);
        if ($id) {
            $b = EmployeeBonus::findOrFail($id);
            $this->editingId = $b->id;
            $this->employeeId = $b->employee_id;
            $this->schemeId = $b->scheme_id;
            $this->amount = $b->amount;
            $this->notes = $b->notes ?? '';
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $data = ['employee_id' => $this->employeeId, 'scheme_id' => $this->schemeId ?: null, 'amount' => $this->amount, 'notes' => $this->notes ?: null];
        if ($this->editingId) EmployeeBonus::findOrFail($this->editingId)->update($data);
        else EmployeeBonus::create(['tenant_id' => auth()->user()->tenant_id, 'status' => 'pending', ...$data]);
        $this->showForm = false;
    }

    public function approve(int $id): void { EmployeeBonus::findOrFail($id)->update(['status' => 'approved', 'approved_by' => auth()->id()]); }
    public function deleteBonus(int $id): void { EmployeeBonus::findOrFail($id)->delete(); }

    // Scheme CRUD
    public function openSchemeForm(?int $id = null): void
    {
        $this->reset(['editingSchemeId', 'schemeName', 'schemeType', 'percentage', 'fixedAmount', 'period']);
        $this->schemeType = 'fixed';
        $this->period = 'annually';
        if ($id) {
            $s = BonusScheme::findOrFail($id);
            $this->editingSchemeId = $s->id;
            $this->schemeName = $s->name;
            $this->schemeType = $s->type;
            $this->percentage = $s->percentage;
            $this->fixedAmount = $s->fixed_amount;
            $this->period = $s->period;
        }
        $this->showSchemeForm = true;
    }

    public function saveScheme(): void
    {
        $this->validate(['schemeName' => 'required|string|max:255']);
        $data = ['name' => $this->schemeName, 'type' => $this->schemeType, 'percentage' => $this->percentage, 'fixed_amount' => $this->fixedAmount, 'period' => $this->period];
        if ($this->editingSchemeId) BonusScheme::findOrFail($this->editingSchemeId)->update($data);
        else BonusScheme::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        $this->showSchemeForm = false;
    }

    public function deleteScheme(int $id): void { BonusScheme::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.payroll.bonus-manager')
            ->layout('layouts.app', ['pageTitle' => 'Bonus']);
    }
}
