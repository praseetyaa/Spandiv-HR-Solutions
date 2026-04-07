<?php
namespace App\Notifications;
use App\Models\CandidateTestAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PsychTestAssignedNotification extends Notification
{
    use Queueable;
    public function __construct(public CandidateTestAssignment $assignment) {}
    public function via(): array { return ['mail']; }
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->subject("Undangan Tes Psikologi - {$this->assignment->test->name}")->line("Anda telah ditugaskan untuk mengikuti tes psikologi.")->line("Batas waktu: {$this->assignment->deadline_at->format('d/m/Y H:i')}");
    }
}
