<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Services\ReportGeneratorService;
use Illuminate\Support\Facades\Storage;

class PayslipController extends Controller
{
    public function show(Payroll $payroll)
    {
        $this->authorize('view', $payroll);
        $payroll->load(['employee.department', 'employee.position', 'employee.detail', 'items', 'period']);
        return view('payroll.payslip', compact('payroll'));
    }

    public function download(Payroll $payroll, ReportGeneratorService $report)
    {
        $this->authorize('view', $payroll);

        if (!$payroll->payslip_path || !Storage::exists($payroll->payslip_path)) {
            $path = $report->generatePayslipPdf($payroll);
            $payroll->update(['payslip_path' => $path]);
        } else {
            $path = $payroll->payslip_path;
        }

        return Storage::download($path);
    }
}
