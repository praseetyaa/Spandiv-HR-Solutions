<?php
namespace App\Notifications;
use App\Models\EmployeeContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ContractExpiringNotification extends Notification
{
    use Queueable;
    public function __construct(public EmployeeContract $contract, public int $daysRemaining) {}
    public function via(): array { return ['mail', 'database']; }
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->subject("Kontrak Karyawan Segera Habis ({$this->daysRemaining} Hari)")->line("Kontrak {$this->contract->employee->full_name} akan berakhir pada {$this->contract->end_date->format('d/m/Y')}.")->action('Lihat Detail', route('employees.index'));
    }
    public function toArray(): array { return ['contract_id' => $this->contract->id, 'days' => $this->daysRemaining, 'type' => 'contract_expiring']; }
}
