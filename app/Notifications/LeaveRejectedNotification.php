<?php
namespace App\Notifications;
use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveRejectedNotification extends Notification
{
    use Queueable;
    public function __construct(public LeaveRequest $leaveRequest) {}
    public function via(): array { return ['mail', 'database']; }
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->subject('Cuti Ditolak')->line("Cuti {$this->leaveRequest->leaveType->name} Anda ditolak.")->line("Alasan: {$this->leaveRequest->rejection_reason}");
    }
    public function toArray(): array { return ['leave_request_id' => $this->leaveRequest->id, 'type' => 'leave_rejected']; }
}
