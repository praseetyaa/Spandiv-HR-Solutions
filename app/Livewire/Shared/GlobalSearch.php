<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class GlobalSearch extends Component
{
    public string $query = '';
    public array $results = [];
    public bool $showResults = false;

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->showResults = false;
            return;
        }

        $tenantId = auth()->user()->tenant_id;
        $term = '%' . $this->query . '%';
        $results = [];

        // Search employees
        $employees = DB::table('employees')
            ->where('tenant_id', $tenantId)
            ->where(fn ($q) => $q->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)->orWhere('employee_number', 'like', $term))
            ->limit(5)
            ->get();

        foreach ($employees as $e) {
            $results[] = ['type' => 'Karyawan', 'label' => "{$e->first_name} {$e->last_name}", 'sub' => $e->employee_number, 'url' => route('employees.index')];
        }

        // Search departments
        $depts = DB::table('departments')
            ->where('tenant_id', $tenantId)
            ->where('name', 'like', $term)
            ->limit(3)
            ->get();

        foreach ($depts as $d) {
            $results[] = ['type' => 'Departemen', 'label' => $d->name, 'sub' => '', 'url' => route('employees.departments')];
        }

        // Search job postings
        $jobs = DB::table('job_postings')
            ->where('tenant_id', $tenantId)
            ->where('title', 'like', $term)
            ->limit(3)
            ->get();

        foreach ($jobs as $j) {
            $results[] = ['type' => 'Lowongan', 'label' => $j->title, 'sub' => $j->status, 'url' => route('recruitment.postings')];
        }

        $this->results = $results;
        $this->showResults = count($results) > 0;
    }

    public function clear(): void
    {
        $this->query = '';
        $this->results = [];
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.shared.global-search');
    }
}
