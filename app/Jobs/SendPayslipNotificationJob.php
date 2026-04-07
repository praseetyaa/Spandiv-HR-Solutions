<?php
namespace App\Jobs;

use App\Models\Payroll;
use App\Services\NotificationDispatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPayslipNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $payrollId) {}

    public function handle(NotificationDispatchService $notif): void
    {
        $payroll = Payroll::with('employee.user', 'period')->findOrFail($this->payrollId);
        if (!$payroll->employee->user) return;

        $notif->send('payslip_ready', $payroll->employee->user, [
            'title'      => 'Slip Gaji Tersedia',
            'message'    => "Slip gaji bulan {$payroll->period->month}/{$payroll->period->year} sudah tersedia.",
            'action_url' => route('payroll.index'),
        ]);
    }
}
