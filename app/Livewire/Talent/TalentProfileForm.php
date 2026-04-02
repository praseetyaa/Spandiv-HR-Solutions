<?php

namespace App\Livewire\Talent;

use App\Models\Employee;
use App\Models\TalentProfile;
use Livewire\Component;

class TalentProfileForm extends Component
{
    public ?int $profileId = null;
    public ?int $employeeId = null;

    public string $potential_level = 'medium';
    public string $performance_level = 'meets';
    public string $flight_risk = 'low';
    public bool $is_successor_ready = false;
    public string $strengths = '';
    public string $development_notes = '';

    protected function rules(): array
    {
        return [
            'employeeId'         => 'required|exists:employees,id',
            'potential_level'    => 'required|in:low,medium,high,very_high',
            'performance_level'  => 'required|in:below,meets,exceeds,outstanding',
            'flight_risk'        => 'required|in:low,medium,high',
            'is_successor_ready' => 'boolean',
            'strengths'          => 'nullable|string|max:2000',
            'development_notes'  => 'nullable|string|max:2000',
        ];
    }

    public function mount(?int $profileId = null): void
    {
        if ($profileId) {
            $profile = TalentProfile::findOrFail($profileId);
            $this->profileId = $profile->id;
            $this->employeeId = $profile->employee_id;
            $this->potential_level = $profile->potential_level;
            $this->performance_level = $profile->performance_level;
            $this->flight_risk = $profile->flight_risk;
            $this->is_successor_ready = $profile->is_successor_ready;
            $this->strengths = $profile->strengths ?? '';
            $this->development_notes = $profile->development_notes ?? '';
        }
    }

    public function getEmployeesProperty()
    {
        // Exclude employees that already have a talent profile (unless editing)
        return Employee::active()
            ->when($this->profileId, function ($q) {
                // Include current profile's employee
            })
            ->whereDoesntHave('talentProfile', function ($q) {
                if ($this->profileId) {
                    $q->where('id', '!=', $this->profileId);
                }
            })
            ->orderBy('first_name')
            ->get();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'employee_id'        => $this->employeeId,
            'potential_level'    => $this->potential_level,
            'performance_level'  => $this->performance_level,
            'flight_risk'        => $this->flight_risk,
            'is_successor_ready' => $this->is_successor_ready,
            'strengths'          => $this->strengths ?: null,
            'development_notes'  => $this->development_notes ?: null,
            'updated_by'         => auth()->id(),
        ];

        if ($this->profileId) {
            TalentProfile::findOrFail($this->profileId)->update($data);
        } else {
            $data['tenant_id'] = auth()->user()->tenant_id;
            TalentProfile::create($data);
        }

        $this->dispatch('profileSaved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.talent.talent-profile-form');
    }
}
