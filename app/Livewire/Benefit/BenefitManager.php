<?php

namespace App\Livewire\Benefit;

use App\Models\BenefitPlan;
use App\Models\BenefitType;
use App\Models\Employee;
use App\Models\EmployeeBenefit;
use Livewire\Component;

class BenefitManager extends Component
{
    public string $tab = 'enrollment'; // enrollment | types | plans
    public string $search = '';
    public string $categoryFilter = '';

    // Type form
    public bool $showTypeForm = false;
    public ?int $editingTypeId = null;
    public string $typeName = '';
    public string $typeCategory = 'allowance';
    public string $typeDescription = '';
    public bool $typeIsMandatory = false;

    // Plan form
    public bool $showPlanForm = false;
    public ?int $editingPlanId = null;
    public ?int $planTypeId = null;
    public string $planName = '';
    public string $planProvider = '';
    public ?float $planCoverage = null;
    public string $planCoverageType = 'fixed';

    // Enrollment form
    public bool $showEnrollForm = false;
    public ?int $editingEnrollId = null;
    public ?int $enrollEmployeeId = null;
    public ?int $enrollPlanId = null;
    public ?string $enrollEffectiveDate = null;
    public float $enrollEmployeeContrib = 0;
    public float $enrollEmployerContrib = 0;

    public function getTypesProperty() { return BenefitType::withCount('plans')->latest()->get(); }
    public function getPlansProperty() { return BenefitPlan::with('benefitType')->withCount('employeeBenefits')->latest()->get(); }
    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function getEnrollmentsProperty()
    {
        return EmployeeBenefit::with(['employee.department', 'plan.benefitType'])
            ->when($this->search, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%")))
            ->latest()
            ->get();
    }

    // ── Type CRUD ──
    public function openTypeForm(?int $id = null): void
    {
        $this->reset(['editingTypeId', 'typeName', 'typeCategory', 'typeDescription', 'typeIsMandatory']);
        if ($id) {
            $t = BenefitType::findOrFail($id);
            $this->editingTypeId = $t->id;
            $this->typeName = $t->name;
            $this->typeCategory = $t->category;
            $this->typeDescription = $t->description ?? '';
            $this->typeIsMandatory = $t->is_mandatory;
        }
        $this->showTypeForm = true;
    }

    public function saveType(): void
    {
        $this->validate(['typeName' => 'required|string|max:255', 'typeCategory' => 'required']);
        $data = ['name' => $this->typeName, 'category' => $this->typeCategory, 'description' => $this->typeDescription ?: null, 'is_mandatory' => $this->typeIsMandatory];
        if ($this->editingTypeId) BenefitType::findOrFail($this->editingTypeId)->update($data);
        else BenefitType::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        $this->showTypeForm = false;
    }

    public function deleteType(int $id): void { BenefitType::findOrFail($id)->delete(); }

    // ── Plan CRUD ──
    public function openPlanForm(?int $id = null): void
    {
        $this->reset(['editingPlanId', 'planTypeId', 'planName', 'planProvider', 'planCoverage', 'planCoverageType']);
        if ($id) {
            $p = BenefitPlan::findOrFail($id);
            $this->editingPlanId = $p->id;
            $this->planTypeId = $p->benefit_type_id;
            $this->planName = $p->name;
            $this->planProvider = $p->provider ?? '';
            $this->planCoverage = $p->coverage_amount;
            $this->planCoverageType = $p->coverage_type;
        }
        $this->showPlanForm = true;
    }

    public function savePlan(): void
    {
        $this->validate(['planTypeId' => 'required|exists:benefit_types,id', 'planName' => 'required|string|max:255']);
        $data = ['benefit_type_id' => $this->planTypeId, 'name' => $this->planName, 'provider' => $this->planProvider ?: null, 'coverage_amount' => $this->planCoverage, 'coverage_type' => $this->planCoverageType];
        if ($this->editingPlanId) BenefitPlan::findOrFail($this->editingPlanId)->update($data);
        else BenefitPlan::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        $this->showPlanForm = false;
    }

    public function deletePlan(int $id): void { BenefitPlan::findOrFail($id)->delete(); }

    // ── Enrollment CRUD ──
    public function openEnrollForm(?int $id = null): void
    {
        $this->reset(['editingEnrollId', 'enrollEmployeeId', 'enrollPlanId', 'enrollEffectiveDate', 'enrollEmployeeContrib', 'enrollEmployerContrib']);
        if ($id) {
            $e = EmployeeBenefit::findOrFail($id);
            $this->editingEnrollId = $e->id;
            $this->enrollEmployeeId = $e->employee_id;
            $this->enrollPlanId = $e->plan_id;
            $this->enrollEffectiveDate = $e->effective_date->format('Y-m-d');
            $this->enrollEmployeeContrib = $e->employee_contribution;
            $this->enrollEmployerContrib = $e->employer_contribution;
        }
        $this->showEnrollForm = true;
    }

    public function saveEnroll(): void
    {
        $this->validate(['enrollEmployeeId' => 'required|exists:employees,id', 'enrollPlanId' => 'required|exists:benefit_plans,id', 'enrollEffectiveDate' => 'required|date']);
        $data = ['employee_id' => $this->enrollEmployeeId, 'plan_id' => $this->enrollPlanId, 'effective_date' => $this->enrollEffectiveDate, 'employee_contribution' => $this->enrollEmployeeContrib, 'employer_contribution' => $this->enrollEmployerContrib];
        if ($this->editingEnrollId) EmployeeBenefit::findOrFail($this->editingEnrollId)->update($data);
        else EmployeeBenefit::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        $this->showEnrollForm = false;
    }

    public function deleteEnroll(int $id): void { EmployeeBenefit::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.benefit.benefit-manager')
            ->layout('layouts.app', ['pageTitle' => 'Benefit Karyawan']);
    }
}
