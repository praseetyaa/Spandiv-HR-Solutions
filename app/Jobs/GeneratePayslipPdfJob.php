<?php
namespace App\Jobs;

use App\Models\Payroll;
use App\Services\ReportGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePayslipPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $payrollId) {}

    public function handle(ReportGeneratorService $report): void
    {
        $payroll = Payroll::findOrFail($this->payrollId);
        $path = $report->generatePayslipPdf($payroll);
        $payroll->update(['payslip_path' => $path]);
    }
}
