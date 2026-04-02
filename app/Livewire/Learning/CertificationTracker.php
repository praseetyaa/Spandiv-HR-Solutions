<?php

namespace App\Livewire\Learning;

use App\Models\Certification;
use App\Models\Employee;
use App\Models\EmployeeCertification;
use Livewire\Component;

class CertificationTracker extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public string $tab = 'records'; // 'records' or 'master'

    // Cert Master Form
    public bool $showCertForm = false;
    public ?int $editingCertId = null;
    public string $certName = '';
    public string $certIssuingBody = '';
    public ?int $certValidityMonths = null;
    public bool $certIsMandatory = false;
    public string $certDescription = '';

    // Employee Cert Form
    public bool $showEmpCertForm = false;
    public ?int $editingEmpCertId = null;
    public ?int $empCertEmployeeId = null;
    public ?int $empCertCertificationId = null;
    public string $empCertName = '';
    public string $empCertIssuingBody = '';
    public string $empCertNumber = '';
    public ?string $empCertIssuedDate = null;
    public ?string $empCertExpiresDate = null;

    public function getCertificationsProperty()
    {
        return Certification::withCount('employeeCertifications')->latest()->get();
    }

    public function getRecordsProperty()
    {
        return EmployeeCertification::with(['employee.department', 'certification'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();
    }

    public function getEmployeesProperty()
    {
        return Employee::active()->orderBy('first_name')->get();
    }

    public function getExpiringCountProperty(): int
    {
        return EmployeeCertification::where('status', 'active')
            ->whereNotNull('expires_date')
            ->where('expires_date', '<=', now()->addDays(30))
            ->where('expires_date', '>', now())
            ->count();
    }

    public function getExpiredCountProperty(): int
    {
        return EmployeeCertification::where('status', 'active')
            ->whereNotNull('expires_date')
            ->where('expires_date', '<', now())
            ->count();
    }

    // ── Cert Master CRUD ──
    public function openCertForm(?int $id = null): void
    {
        $this->resetCertForm();
        if ($id) {
            $c = Certification::findOrFail($id);
            $this->editingCertId = $c->id;
            $this->certName = $c->name;
            $this->certIssuingBody = $c->issuing_body ?? '';
            $this->certValidityMonths = $c->validity_months;
            $this->certIsMandatory = $c->is_mandatory;
            $this->certDescription = $c->description ?? '';
        }
        $this->showCertForm = true;
    }

    public function saveCert(): void
    {
        $this->validate([
            'certName' => 'required|string|max:255',
        ]);

        $data = [
            'name'            => $this->certName,
            'issuing_body'    => $this->certIssuingBody ?: null,
            'validity_months' => $this->certValidityMonths,
            'is_mandatory'    => $this->certIsMandatory,
            'description'     => $this->certDescription ?: null,
        ];

        if ($this->editingCertId) {
            Certification::findOrFail($this->editingCertId)->update($data);
        } else {
            $data['tenant_id'] = auth()->user()->tenant_id;
            Certification::create($data);
        }

        $this->showCertForm = false;
        $this->resetCertForm();
    }

    public function deleteCert(int $id): void
    {
        Certification::findOrFail($id)->delete();
    }

    private function resetCertForm(): void
    {
        $this->editingCertId = null;
        $this->certName = '';
        $this->certIssuingBody = '';
        $this->certValidityMonths = null;
        $this->certIsMandatory = false;
        $this->certDescription = '';
    }

    // ── Employee Cert CRUD ──
    public function openEmpCertForm(?int $id = null): void
    {
        $this->resetEmpCertForm();
        if ($id) {
            $ec = EmployeeCertification::findOrFail($id);
            $this->editingEmpCertId = $ec->id;
            $this->empCertEmployeeId = $ec->employee_id;
            $this->empCertCertificationId = $ec->certification_id;
            $this->empCertName = $ec->name;
            $this->empCertIssuingBody = $ec->issuing_body ?? '';
            $this->empCertNumber = $ec->certificate_number ?? '';
            $this->empCertIssuedDate = $ec->issued_date->format('Y-m-d');
            $this->empCertExpiresDate = $ec->expires_date?->format('Y-m-d');
        }
        $this->showEmpCertForm = true;
    }

    public function saveEmpCert(): void
    {
        $this->validate([
            'empCertEmployeeId' => 'required|exists:employees,id',
            'empCertName'       => 'required|string|max:255',
            'empCertIssuedDate' => 'required|date',
        ]);

        $data = [
            'employee_id'      => $this->empCertEmployeeId,
            'certification_id' => $this->empCertCertificationId,
            'name'             => $this->empCertName,
            'issuing_body'     => $this->empCertIssuingBody ?: null,
            'certificate_number' => $this->empCertNumber ?: null,
            'issued_date'      => $this->empCertIssuedDate,
            'expires_date'     => $this->empCertExpiresDate ?: null,
        ];

        if ($this->editingEmpCertId) {
            EmployeeCertification::findOrFail($this->editingEmpCertId)->update($data);
        } else {
            $data['tenant_id'] = auth()->user()->tenant_id;
            EmployeeCertification::create($data);
        }

        $this->showEmpCertForm = false;
        $this->resetEmpCertForm();
    }

    public function deleteEmpCert(int $id): void
    {
        EmployeeCertification::findOrFail($id)->delete();
    }

    private function resetEmpCertForm(): void
    {
        $this->editingEmpCertId = null;
        $this->empCertEmployeeId = null;
        $this->empCertCertificationId = null;
        $this->empCertName = '';
        $this->empCertIssuingBody = '';
        $this->empCertNumber = '';
        $this->empCertIssuedDate = null;
        $this->empCertExpiresDate = null;
    }

    public function render()
    {
        return view('livewire.learning.certification-tracker')
            ->layout('layouts.app', ['pageTitle' => 'Sertifikasi']);
    }
}
