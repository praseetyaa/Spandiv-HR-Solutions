<?php

namespace App\Livewire\Settings;

use App\Models\AuditLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogViewer extends Component
{
    use WithPagination;

    public string $search = '';
    public string $actionFilter = '';
    public ?int $userFilter = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingActionFilter() { $this->resetPage(); }

    public function getUsersProperty()
    {
        return User::orderBy('name')->get();
    }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, fn ($q) => $q->where(fn ($sq) => $sq->where('model_type', 'like', "%{$this->search}%")->orWhere('action', 'like', "%{$this->search}%")))
            ->when($this->actionFilter, fn ($q) => $q->where('action', $this->actionFilter))
            ->when($this->userFilter, fn ($q) => $q->where('user_id', $this->userFilter))
            ->when($this->dateFrom, fn ($q) => $q->where('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('created_at', '<=', $this->dateTo . ' 23:59:59'))
            ->latest('created_at')
            ->paginate(25);

        return view('livewire.settings.audit-log-viewer', ['logs' => $logs])
            ->layout('layouts.app', ['pageTitle' => 'Audit Log']);
    }
}
