<?php
namespace App\Notifications;
use App\Models\EmployeeOnboardingTask;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OnboardingTaskDueNotification extends Notification
{
    use Queueable;
    public function __construct(public EmployeeOnboardingTask $task) {}
    public function via(): array { return ['mail', 'database']; }
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->subject('Tugas Onboarding Menunggu')->line("Tugas \"{$this->task->title}\" harus diselesaikan sebelum {$this->task->due_date->format('d/m/Y')}.");
    }
    public function toArray(): array { return ['task_id' => $this->task->id, 'type' => 'onboarding_task_due']; }
}
