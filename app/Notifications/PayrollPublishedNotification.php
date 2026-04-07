<?php
namespace App\Notifications;
use App\Models\PayrollPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PayrollPublishedNotification extends Notification
{
    use Queueable;
    public function __construct(public PayrollPeriod $period, public string $netSalary) {}
    public function via(): array { return ['mail', 'database']; }
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->subject('Slip Gaji Tersedia')->line("Slip gaji periode {$this->period->month}/{$this->period->year} sudah tersedia.")->action('Lihat Slip Gaji', route('payroll.index'));
    }
    public function toArray(): array { return ['period_id' => $this->period->id, 'type' => 'payroll_published']; }
}
