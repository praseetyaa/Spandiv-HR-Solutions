<?php

namespace App\Livewire\Payroll;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use Livewire\Component;

class PayrollManager extends Component
{
    public string $search = '';
    public ?int $selectedPeriodId = null;

    public bool $showForm = false;
    public ?int $editingId = null;
    public int $month = 1;
    public int $year = 2026;
    public ?string $payDate = null;

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function getPeriodsProperty()
    {
        return PayrollPeriod::withCount('payrolls')
            ->orderByDesc('year')->orderByDesc('month')
            ->get();
    }

    public function getPayrollsProperty()
    {
        if (!$this->selectedPeriodId) return collect();
        return Payroll::with(['employee.department', 'employee.jobPosition'])
            ->where('period_id', $this->selectedPeriodId)
            ->when($this->search, fn ($q) => $q->whereHas('employee', fn ($e) =>
                $e->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%")
            ))
            ->get();
    }

    public function getSelectedPeriodProperty()
    {
        if (!$this->selectedPeriodId) return null;
        return PayrollPeriod::find($this->selectedPeriodId);
    }

    public function selectPeriod(int $id): void
    {
        $this->selectedPeriodId = $id;
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'month', 'year', 'payDate']);
        $this->month = now()->month;
        $this->year = now()->year;
        if ($id) {
            $p = PayrollPeriod::findOrFail($id);
            $this->editingId = $p->id;
            $this->month = $p->month;
            $this->year = $p->year;
            $this->payDate = $p->pay_date->format('Y-m-d');
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
            'payDate' => 'required|date',
        ]);

        $data = ['month' => $this->month, 'year' => $this->year, 'pay_date' => $this->payDate];

        if ($this->editingId) {
            PayrollPeriod::findOrFail($this->editingId)->update($data);
        } else {
            PayrollPeriod::create(['tenant_id' => auth()->user()->tenant_id, 'status' => 'draft', ...$data]);
        }
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        PayrollPeriod::findOrFail($id)->delete();
        if ($this->selectedPeriodId === $id) $this->selectedPeriodId = null;
    }

    public function render()
    {
        return view('livewire.payroll.payroll-manager')
            ->layout('layouts.app', ['pageTitle' => 'Penggajian']);
    }
}
