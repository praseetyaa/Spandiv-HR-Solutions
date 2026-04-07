<?php
namespace App\Notifications;
use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AnnouncementNotification extends Notification
{
    use Queueable;
    public function __construct(public Announcement $announcement) {}
    public function via(): array { return ['database']; }
    public function toArray(): array { return ['announcement_id' => $this->announcement->id, 'title' => $this->announcement->title, 'type' => 'announcement']; }
}
