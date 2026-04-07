<?php

namespace App\Livewire\Recruitment;

use App\Models\Employee;
use App\Models\EmployeeOnboarding;
use App\Models\OnboardingTemplate;
use Livewire\Component;

class OnboardingManager extends Component
{
    public string $tab = 'onboardings'; // onboardings | templates
    public string $search = '';
    public string $statusFilter = '';

    // Onboarding form
    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $employeeId = null;
    public ?int $templateId = null;
    public ?string $startDate = null;
    public ?string $expectedEndDate = null;

    // Template form
    public bool $showTemplateForm = false;
    public ?int $editingTemplateId = null;
    public string $templateName = '';
    public string $templateDescription = '';

    public function getOnboardingsProperty()
    {
        return EmployeeOnboarding::with(['employee.department', 'template'])
            ->withCount('tasks')
            ->when($this->search, fn ($q) => $q->whereHas('employee', fn ($e) =>
                $e->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%")
            ))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()->get();
    }

    public function getTemplatesProperty()
    {
        return OnboardingTemplate::withCount(['tasks', 'onboardings'])->orderBy('name')->get();
    }

    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function getStatsProperty()
    {
        return [
            'total'       => EmployeeOnboarding::count(),
            'in_progress' => EmployeeOnboarding::where('status', 'in_progress')->count(),
            'completed'   => EmployeeOnboarding::where('status', 'completed')->count(),
            'overdue'     => EmployeeOnboarding::where('status', 'overdue')->count(),
        ];
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'employeeId', 'templateId', 'startDate', 'expectedEndDate']);
        $this->startDate = now()->format('Y-m-d');
        $this->expectedEndDate = now()->addDays(30)->format('Y-m-d');
        if ($id) {
            $o = EmployeeOnboarding::findOrFail($id);
            $this->editingId = $o->id;
            $this->employeeId = $o->employee_id;
            $this->templateId = $o->template_id;
            $this->startDate = $o->start_date->format('Y-m-d');
            $this->expectedEndDate = $o->expected_end_date->format('Y-m-d');
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'templateId' => 'required|exists:onboarding_templates,id',
            'startDate' => 'required|date',
            'expectedEndDate' => 'required|date|after:startDate',
        ]);

        $data = [
            'employee_id' => $this->employeeId, 'template_id' => $this->templateId,
            'start_date' => $this->startDate, 'expected_end_date' => $this->expectedEndDate,
        ];

        if ($this->editingId) {
            EmployeeOnboarding::findOrFail($this->editingId)->update($data);
        } else {
            EmployeeOnboarding::create([
                'tenant_id' => auth()->user()->tenant_id,
                'status' => 'not_started', 'progress_percent' => 0,
                'created_by' => auth()->id(), ...$data,
            ]);
        }
        $this->showForm = false;
    }

    public function deleteOnboarding(int $id): void { EmployeeOnboarding::findOrFail($id)->delete(); }

    // Template CRUD
    public function openTemplateForm(?int $id = null): void
    {
        $this->reset(['editingTemplateId', 'templateName', 'templateDescription']);
        if ($id) {
            $t = OnboardingTemplate::findOrFail($id);
            $this->editingTemplateId = $t->id;
            $this->templateName = $t->name;
            $this->templateDescription = $t->description ?? '';
        }
        $this->showTemplateForm = true;
    }

    public function saveTemplate(): void
    {
        $this->validate(['templateName' => 'required|string|max:255']);
        $data = ['name' => $this->templateName, 'description' => $this->templateDescription ?: null];
        if ($this->editingTemplateId) OnboardingTemplate::findOrFail($this->editingTemplateId)->update($data);
        else OnboardingTemplate::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        $this->showTemplateForm = false;
    }

    public function deleteTemplate(int $id): void { OnboardingTemplate::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.recruitment.onboarding-manager')
            ->layout('layouts.app', ['pageTitle' => 'Onboarding']);
    }
}
