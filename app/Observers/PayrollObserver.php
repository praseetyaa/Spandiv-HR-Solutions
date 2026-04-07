<?php
namespace App\Observers;

use App\Events\PayrollPublished;
use App\Models\Payroll;

class PayrollObserver
{
    public function updated(Payroll $payroll): void
    {
        if ($payroll->wasChanged('status') && $payroll->status === 'finalized') {
            // Trigger PDF generation — all payrolls in this period
            PayrollPublished::dispatch($payroll->period);
        }
    }
}
