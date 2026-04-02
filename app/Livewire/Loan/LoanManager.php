<?php

namespace App\Livewire\Loan;

use App\Models\Employee;
use App\Models\EmployeeLoan;
use Livewire\Component;

class LoanManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';

    // Form
    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $employeeId = null;
    public float $loanAmount = 0;
    public int $installmentMonths = 12;
    public ?string $startDate = null;
    public string $notes = '';

    public function getLoansProperty()
    {
        return EmployeeLoan::with('employee')
            ->when($this->search, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('first_name', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();
    }

    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function getTotalOutstandingProperty(): float
    {
        return EmployeeLoan::where('status', 'active')->sum('remaining_amount');
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'employeeId', 'loanAmount', 'installmentMonths', 'startDate', 'notes']);
        $this->installmentMonths = 12;
        if ($id) {
            $l = EmployeeLoan::findOrFail($id);
            $this->editingId = $l->id;
            $this->employeeId = $l->employee_id;
            $this->loanAmount = $l->loan_amount;
            $this->installmentMonths = $l->installment_months;
            $this->startDate = $l->start_date->format('Y-m-d');
            $this->notes = $l->notes ?? '';
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'employeeId'        => 'required|exists:employees,id',
            'loanAmount'        => 'required|numeric|min:1',
            'installmentMonths' => 'required|integer|min:1',
            'startDate'         => 'required|date',
        ]);

        $monthlyDeduction = round($this->loanAmount / $this->installmentMonths, 2);

        $data = [
            'employee_id'       => $this->employeeId,
            'loan_amount'       => $this->loanAmount,
            'installment_months' => $this->installmentMonths,
            'monthly_deduction' => $monthlyDeduction,
            'start_date'        => $this->startDate,
            'remaining_amount'  => $this->loanAmount,
            'notes'             => $this->notes ?: null,
        ];

        if ($this->editingId) {
            EmployeeLoan::findOrFail($this->editingId)->update($data);
        } else {
            $data['tenant_id'] = auth()->user()->tenant_id;
            EmployeeLoan::create($data);
        }

        $this->showForm = false;
    }

    public function approveLoan(int $id): void
    {
        EmployeeLoan::findOrFail($id)->update([
            'status' => 'active', 'approved_by' => auth()->id(), 'approved_at' => now(),
        ]);
    }

    public function deleteLoan(int $id): void { EmployeeLoan::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.loan.loan-manager')
            ->layout('layouts.app', ['pageTitle' => 'Pinjaman Karyawan']);
    }
}
