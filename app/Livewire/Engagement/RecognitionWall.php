<?php

namespace App\Livewire\Engagement;

use App\Models\Employee;
use App\Models\Recognition;
use Livewire\Component;

class RecognitionWall extends Component
{
    public string $search = '';
    public string $badgeFilter = '';

    // Form
    public bool $showForm = false;
    public ?int $giverId = null;
    public ?int $receiverId = null;
    public string $badgeType = 'teamwork';
    public string $message = '';
    public int $points = 10;

    public function getRecognitionsProperty()
    {
        return Recognition::with(['giver', 'receiver'])
            ->when($this->search, fn ($q) => $q->where('message', 'like', "%{$this->search}%"))
            ->when($this->badgeFilter, fn ($q) => $q->where('badge_type', $this->badgeFilter))
            ->where('is_public', true)
            ->latest()
            ->get();
    }

    public function getLeaderboardProperty()
    {
        return Employee::select('employees.*')
            ->selectRaw('COALESCE((SELECT SUM(points) FROM recognitions WHERE receiver_id = employees.id), 0) as total_points')
            ->selectRaw('COALESCE((SELECT COUNT(*) FROM recognitions WHERE receiver_id = employees.id), 0) as total_recognitions')
            ->having('total_points', '>', 0)
            ->orderByDesc('total_points')
            ->limit(10)
            ->get();
    }

    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function getTotalRecognitionsProperty(): int { return Recognition::count(); }
    public function getTotalPointsProperty(): int { return Recognition::sum('points'); }

    public function openForm(): void
    {
        $this->reset(['giverId', 'receiverId', 'badgeType', 'message', 'points']);
        $this->badgeType = 'teamwork';
        $this->points = 10;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'giverId' => 'required|exists:employees,id',
            'receiverId' => 'required|exists:employees,id|different:giverId',
            'badgeType' => 'required|string',
            'message' => 'required|string',
        ]);

        Recognition::create([
            'tenant_id' => auth()->user()->tenant_id,
            'giver_id' => $this->giverId,
            'receiver_id' => $this->receiverId,
            'badge_type' => $this->badgeType,
            'message' => $this->message,
            'is_public' => true,
            'points' => $this->points,
        ]);
        $this->showForm = false;
    }

    public function delete(int $id): void { Recognition::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.engagement.recognition-wall')
            ->layout('layouts.app', ['pageTitle' => 'Wall of Recognition']);
    }
}
