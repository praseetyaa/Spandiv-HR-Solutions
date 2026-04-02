<?php

namespace App\Livewire\Engagement;

use App\Models\Announcement;
use App\Models\Department;
use Livewire\Component;

class AnnouncementManager extends Component
{
    public string $search = '';
    public string $priorityFilter = '';

    // Form
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $announcementTitle = '';
    public string $announcementContent = '';
    public string $announcementPriority = 'normal';
    public ?string $publishAt = null;
    public ?string $expiresAt = null;

    public function getAnnouncementsProperty()
    {
        return Announcement::with('creator')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->priorityFilter, fn ($q) => $q->where('priority', $this->priorityFilter))
            ->latest('publish_at')
            ->get();
    }

    public function getDepartmentsProperty() { return Department::orderBy('name')->get(); }

    public function getStatsProperty(): array
    {
        return [
            'total' => Announcement::count(),
            'published' => Announcement::where('is_published', true)->count(),
            'urgent' => Announcement::where('priority', 'urgent')->where('is_published', true)->count(),
        ];
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'announcementTitle', 'announcementContent', 'announcementPriority', 'publishAt', 'expiresAt']);
        $this->announcementPriority = 'normal';
        $this->publishAt = now()->format('Y-m-d\TH:i');
        if ($id) {
            $a = Announcement::findOrFail($id);
            $this->editingId = $a->id;
            $this->announcementTitle = $a->title;
            $this->announcementContent = $a->content;
            $this->announcementPriority = $a->priority;
            $this->publishAt = $a->publish_at->format('Y-m-d\TH:i');
            $this->expiresAt = $a->expires_at?->format('Y-m-d\TH:i');
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'announcementTitle' => 'required|string|max:255',
            'announcementContent' => 'required|string',
            'publishAt' => 'required|date',
        ]);

        $data = [
            'title' => $this->announcementTitle,
            'content' => $this->announcementContent,
            'priority' => $this->announcementPriority,
            'publish_at' => $this->publishAt,
            'expires_at' => $this->expiresAt ?: null,
        ];

        if ($this->editingId) {
            Announcement::findOrFail($this->editingId)->update($data);
        } else {
            Announcement::create([
                'tenant_id' => auth()->user()->tenant_id,
                'created_by' => auth()->id(),
                'is_published' => false,
                ...$data,
            ]);
        }
        $this->showForm = false;
    }

    public function togglePublish(int $id): void
    {
        $a = Announcement::findOrFail($id);
        $a->update(['is_published' => !$a->is_published]);
    }

    public function delete(int $id): void { Announcement::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.engagement.announcement-manager')
            ->layout('layouts.app', ['pageTitle' => 'Pengumuman']);
    }
}
