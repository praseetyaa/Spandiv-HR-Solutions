<?php
namespace App\Listeners;

use App\Events\PayrollPublished;
use App\Models\Payroll;
use App\Services\NotificationDispatchService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPayrollNotification implements ShouldQueue
{
    public function __construct(private NotificationDispatchService $notif) {}

    public function handle(PayrollPublished $event): void
    {
        $payrolls = Payroll::with('employee.user')
            ->where('period_id', $event->period->id)
            ->where('status', 'finalized')
            ->get();

        foreach ($payrolls as $payroll) {
            if (!$payroll->employee->user) continue;

            $this->notif->send('payroll_published', $payroll->employee->user, [
                'month' => $event->period->month,
                'year'  => $event->period->year,
                'net_salary' => number_format($payroll->net_salary, 0, ',', '.'),
            ]);
        }
    }
}
