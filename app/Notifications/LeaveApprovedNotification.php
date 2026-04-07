<?php
namespace App\Notifications;
use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveApprovedNotification extends Notification
{
    use Queueable;
    public function __construct(public LeaveRequest $leaveRequest) {}
    public function via(): array { return ['mail', 'database']; }
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->subject('Cuti Disetujui')->line("Cuti {$this->leaveRequest->leaveType->name} Anda telah disetujui.")->line("Tanggal: {$this->leaveRequest->start_date->format('d/m/Y')} — {$this->leaveRequest->end_date->format('d/m/Y')}");
    }
    public function toArray(): array { return ['leave_request_id' => $this->leaveRequest->id, 'type' => 'leave_approved']; }
}
