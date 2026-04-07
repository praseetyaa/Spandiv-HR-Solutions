<?php

namespace App\Livewire\Payroll;

use App\Models\SalaryComponent;
use App\Models\SalaryGrade;
use Livewire\Component;

class SalaryComponentManager extends Component
{
    public string $tab = 'components'; // components | grades
    public string $search = '';

    // Component form
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $type = 'allowance';
    public string $calculationType = 'fixed';
    public float $defaultAmount = 0;
    public bool $isTaxable = true;
    public bool $isMandatory = false;

    // Grade form
    public bool $showGradeForm = false;
    public ?int $editingGradeId = null;
    public string $gradeName = '';
    public string $gradeCode = '';
    public int $gradeLevel = 1;

    public function getComponentsProperty()
    {
        return SalaryComponent::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('type')->orderBy('name')
            ->get();
    }

    public function getGradesProperty()
    {
        return SalaryGrade::withCount('employeeSalaries')
            ->orderBy('level')
            ->get();
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'name', 'type', 'calculationType', 'defaultAmount', 'isTaxable', 'isMandatory']);
        $this->type = 'allowance';
        $this->calculationType = 'fixed';
        $this->isTaxable = true;
        if ($id) {
            $c = SalaryComponent::findOrFail($id);
            $this->editingId = $c->id;
            $this->name = $c->name;
            $this->type = $c->type;
            $this->calculationType = $c->calculation_type;
            $this->defaultAmount = $c->default_amount;
            $this->isTaxable = $c->is_taxable;
            $this->isMandatory = $c->is_mandatory;
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate(['name' => 'required|string|max:255']);
        $data = [
            'name' => $this->name, 'type' => $this->type,
            'calculation_type' => $this->calculationType,
            'default_amount' => $this->defaultAmount,
            'is_taxable' => $this->isTaxable,
            'is_mandatory' => $this->isMandatory,
        ];
        if ($this->editingId) SalaryComponent::findOrFail($this->editingId)->update($data);
        else SalaryComponent::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        $this->showForm = false;
    }

    public function deleteComponent(int $id): void { SalaryComponent::findOrFail($id)->delete(); }

    // Grade CRUD
    public function openGradeForm(?int $id = null): void
    {
        $this->reset(['editingGradeId', 'gradeName', 'gradeCode', 'gradeLevel']);
        $this->gradeLevel = 1;
        if ($id) {
            $g = SalaryGrade::findOrFail($id);
            $this->editingGradeId = $g->id;
            $this->gradeName = $g->name;
            $this->gradeCode = $g->code;
            $this->gradeLevel = $g->level;
        }
        $this->showGradeForm = true;
    }

    public function saveGrade(): void
    {
        $this->validate(['gradeName' => 'required|string|max:255', 'gradeCode' => 'required|string|max:20']);
        $data = ['name' => $this->gradeName, 'code' => $this->gradeCode, 'level' => $this->gradeLevel];
        if ($this->editingGradeId) SalaryGrade::findOrFail($this->editingGradeId)->update($data);
        else SalaryGrade::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        $this->showGradeForm = false;
    }

    public function deleteGrade(int $id): void { SalaryGrade::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.payroll.salary-component-manager')
            ->layout('layouts.app', ['pageTitle' => 'Komponen Gaji']);
    }
}
