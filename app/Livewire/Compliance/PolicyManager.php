<?php

namespace App\Livewire\Compliance;

use App\Models\CompanyPolicy;
use App\Models\PolicyVersion;
use App\Models\PolicyAcknowledgment;
use App\Models\Employee;
use Livewire\Component;

class PolicyManager extends Component
{
    public string $tab = 'policies'; // policies | versions | acknowledgments
    public string $search = '';
    public string $categoryFilter = '';

    // Policy form
    public bool $showPolicyForm = false;
    public ?int $editingPolicyId = null;
    public string $policyTitle = '';
    public string $policyCategory = 'hr';
    public string $policyCode = '';
    public string $policyDescription = '';
    public bool $policyRequiresAck = true;

    // Selected policy for detail
    public ?int $selectedPolicyId = null;

    // Version form
    public bool $showVersionForm = false;
    public string $versionContent = '';
    public ?string $versionEffectiveDate = null;

    public function getPoliciesProperty()
    {
        return CompanyPolicy::withCount(['versions', 'acknowledgments'])
            ->with('currentVersion')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn ($q) => $q->where('category', $this->categoryFilter))
            ->latest()
            ->get();
    }

    public function getSelectedPolicyProperty()
    {
        if (!$this->selectedPolicyId) return null;
        return CompanyPolicy::with(['versions' => fn ($q) => $q->latest('version_number'), 'currentVersion'])->find($this->selectedPolicyId);
    }

    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function selectPolicy(int $id): void { $this->selectedPolicyId = $id; }

    // ── Policy CRUD ──
    public function openPolicyForm(?int $id = null): void
    {
        $this->reset(['editingPolicyId', 'policyTitle', 'policyCategory', 'policyCode', 'policyDescription', 'policyRequiresAck']);
        $this->policyRequiresAck = true;
        $this->policyCategory = 'hr';
        if ($id) {
            $p = CompanyPolicy::findOrFail($id);
            $this->editingPolicyId = $p->id;
            $this->policyTitle = $p->title;
            $this->policyCategory = $p->category;
            $this->policyCode = $p->code ?? '';
            $this->policyDescription = $p->description ?? '';
            $this->policyRequiresAck = $p->requires_acknowledgment;
        }
        $this->showPolicyForm = true;
    }

    public function savePolicy(): void
    {
        $this->validate(['policyTitle' => 'required|string|max:255', 'policyCategory' => 'required']);
        $data = [
            'title' => $this->policyTitle,
            'category' => $this->policyCategory,
            'code' => $this->policyCode ?: null,
            'description' => $this->policyDescription ?: null,
            'requires_acknowledgment' => $this->policyRequiresAck,
        ];
        if ($this->editingPolicyId) {
            CompanyPolicy::findOrFail($this->editingPolicyId)->update($data);
        } else {
            CompanyPolicy::create([
                'tenant_id' => auth()->user()->tenant_id,
                'created_by' => auth()->id(),
                ...$data,
            ]);
        }
        $this->showPolicyForm = false;
    }

    public function toggleActive(int $id): void
    {
        $p = CompanyPolicy::findOrFail($id);
        $p->update(['is_active' => !$p->is_active]);
    }

    public function deletePolicy(int $id): void
    {
        CompanyPolicy::findOrFail($id)->delete();
        if ($this->selectedPolicyId === $id) $this->selectedPolicyId = null;
    }

    // ── Version ──
    public function openVersionForm(): void
    {
        $this->reset(['versionContent', 'versionEffectiveDate']);
        $this->versionEffectiveDate = now()->format('Y-m-d');
        $this->showVersionForm = true;
    }

    public function saveVersion(): void
    {
        $this->validate(['versionContent' => 'required|string', 'versionEffectiveDate' => 'required|date']);
        $policy = CompanyPolicy::findOrFail($this->selectedPolicyId);

        // Set all existing versions to not current
        $policy->versions()->update(['is_current' => false]);

        $nextVersion = $policy->versions()->max('version_number') + 1;
        PolicyVersion::create([
            'policy_id' => $policy->id,
            'version_number' => $nextVersion ?: 1,
            'content' => $this->versionContent,
            'created_by' => auth()->id(),
            'effective_date' => $this->versionEffectiveDate,
            'is_current' => true,
        ]);
        $this->showVersionForm = false;
    }

    public function render()
    {
        return view('livewire.compliance.policy-manager')
            ->layout('layouts.app', ['pageTitle' => 'Kebijakan Perusahaan']);
    }
}
