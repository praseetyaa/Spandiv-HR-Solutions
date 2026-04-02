<?php

namespace App\Livewire\Talent;

use App\Models\Department;
use App\Models\TalentProfile;
use Livewire\Component;

class TalentGrid extends Component
{
    public ?int $departmentFilter = null;
    public ?int $selectedCell = null;
    public int $selectedX = -1;
    public int $selectedY = -1;
    public bool $showProfileForm = false;
    public ?int $editingProfileId = null;

    // Grid cell labels
    public array $potentialLabels = ['Low', 'Medium', 'High'];
    public array $performanceLabels = ['Below', 'Meets', 'Exceeds'];

    // 9-box cell meta
    public array $cellMeta = [
        '0-2' => ['label' => 'Enigma', 'desc' => 'High potential, low performance', 'color' => 'purple'],
        '1-2' => ['label' => 'Growth Potential', 'desc' => 'High potential, average performance', 'color' => 'teal'],
        '2-2' => ['label' => 'Star', 'desc' => 'Top talent', 'color' => 'emerald'],
        '0-1' => ['label' => 'Inconsistent', 'desc' => 'Average potential, low performance', 'color' => 'orange'],
        '1-1' => ['label' => 'Core Player', 'desc' => 'Solid contributor', 'color' => 'slate'],
        '2-1' => ['label' => 'High Performer', 'desc' => 'Strong results, moderate potential', 'color' => 'sky'],
        '0-0' => ['label' => 'Risk', 'desc' => 'Low potential & performance', 'color' => 'red'],
        '1-0' => ['label' => 'Average', 'desc' => 'Moderate performer, low potential', 'color' => 'amber'],
        '2-0' => ['label' => 'Strong Performer', 'desc' => 'Reliable, limited growth', 'color' => 'blue'],
    ];

    protected $listeners = ['profileSaved' => '$refresh'];

    public function getDepartmentsProperty()
    {
        return Department::orderBy('name')->get();
    }

    public function getProfilesProperty()
    {
        return TalentProfile::with('employee.department', 'employee.jobPosition')
            ->byDepartment($this->departmentFilter)
            ->get();
    }

    public function getGridDataProperty(): array
    {
        $grid = [];

        for ($y = 0; $y <= 2; $y++) {
            for ($x = 0; $x <= 2; $x++) {
                $grid["{$x}-{$y}"] = [];
            }
        }

        foreach ($this->profiles as $profile) {
            $key = "{$profile->grid_x}-{$profile->grid_y}";
            if (isset($grid[$key])) {
                $grid[$key][] = $profile;
            }
        }

        return $grid;
    }

    public function selectCell(int $x, int $y): void
    {
        $this->selectedX = $x;
        $this->selectedY = $y;
    }

    public function openProfileForm(?int $profileId = null): void
    {
        $this->editingProfileId = $profileId;
        $this->showProfileForm = true;
    }

    public function closeProfileForm(): void
    {
        $this->showProfileForm = false;
        $this->editingProfileId = null;
    }

    public function render()
    {
        return view('livewire.talent.talent-grid')
            ->layout('layouts.app', ['pageTitle' => 'Talent 9-Box Grid']);
    }
}
