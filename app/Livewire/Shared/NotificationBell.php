<?php

namespace App\Livewire\Shared;

use App\Models\Notification;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    public function refreshCount(): void
    {
        $this->unreadCount = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }

    public function markAllRead(): void
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->unreadCount = 0;
    }

    public function getNotificationsProperty()
    {
        return Notification::where('user_id', auth()->id())
            ->latest()
            ->limit(10)
            ->get();
    }

    public function markAsRead(int $id): void
    {
        Notification::where('id', $id)->where('user_id', auth()->id())
            ->update(['read_at' => now()]);
        $this->refreshCount();
    }

    public function render()
    {
        return view('livewire.shared.notification-bell', [
            'notifications' => $this->notifications,
        ]);
    }
}
