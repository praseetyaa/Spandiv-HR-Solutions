<?php
namespace App\Jobs;

use App\Models\PayrollPeriod;
use App\Services\PayrollCalculatorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPayrollJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $periodId) {}

    public function handle(PayrollCalculatorService $calculator): void
    {
        $period = PayrollPeriod::findOrFail($this->periodId);
        $count = $calculator->processPayroll($period);

        $period->update([
            'status'          => 'processed',
            'processed_count' => $count,
            'processed_at'    => now(),
        ]);
    }
}
