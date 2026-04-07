<?php
namespace App\Notifications;
use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveRequestedNotification extends Notification
{
    use Queueable;
    public function __construct(public LeaveRequest $leaveRequest) {}
    public function via(): array { return ['mail', 'database']; }
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Permohonan Cuti Baru')
            ->greeting("Halo {$notifiable->name},")
            ->line("{$this->leaveRequest->employee->full_name} mengajukan cuti {$this->leaveRequest->leaveType->name}.")
            ->line("Tanggal: {$this->leaveRequest->start_date->format('d/m/Y')} — {$this->leaveRequest->end_date->format('d/m/Y')}")
            ->action('Lihat Detail', route('attendance.leave'));
    }
    public function toArray(): array { return ['leave_request_id' => $this->leaveRequest->id, 'type' => 'leave_requested']; }
}
