<?php

namespace App\Livewire\Settings;

use App\Models\NotificationTemplate;
use Livewire\Component;

class NotificationManager extends Component
{
    public string $search = '';
    public string $channelFilter = '';

    public bool $showForm = false;
    public ?int $editingId = null;
    public string $eventKey = '';
    public string $channel = 'email';
    public string $subject = '';
    public string $bodyTemplate = '';
    public bool $isActive = true;

    public function getTemplatesProperty()
    {
        return NotificationTemplate::query()
            ->when($this->search, fn ($q) => $q->where('event_key', 'like', "%{$this->search}%"))
            ->when($this->channelFilter, fn ($q) => $q->where('channel', $this->channelFilter))
            ->latest()
            ->get();
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'eventKey', 'channel', 'subject', 'bodyTemplate', 'isActive']);
        $this->channel = 'email';
        $this->isActive = true;
        if ($id) {
            $t = NotificationTemplate::findOrFail($id);
            $this->editingId = $t->id;
            $this->eventKey = $t->event_key;
            $this->channel = $t->channel;
            $this->subject = $t->subject ?? '';
            $this->bodyTemplate = $t->body_template;
            $this->isActive = $t->is_active;
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'eventKey'     => 'required|string|max:100',
            'channel'      => 'required',
            'bodyTemplate' => 'required|string',
        ]);

        $data = [
            'event_key'     => $this->eventKey,
            'channel'       => $this->channel,
            'subject'       => $this->subject ?: null,
            'body_template' => $this->bodyTemplate,
            'is_active'     => $this->isActive,
        ];

        if ($this->editingId) {
            NotificationTemplate::findOrFail($this->editingId)->update($data);
        } else {
            NotificationTemplate::create([
                'tenant_id' => auth()->user()->tenant_id,
                ...$data,
            ]);
        }
        $this->showForm = false;
    }

    public function toggleActive(int $id): void
    {
        $t = NotificationTemplate::findOrFail($id);
        $t->update(['is_active' => !$t->is_active]);
    }

    public function delete(int $id): void
    {
        NotificationTemplate::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.settings.notification-manager')
            ->layout('layouts.app', ['pageTitle' => 'Template Notifikasi']);
    }
}
