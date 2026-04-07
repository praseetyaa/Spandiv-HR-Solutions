<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportGeneratorService
{
    public function attendanceSummary(int $tenantId, int $month, int $year): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $employees = Employee::where('tenant_id', $tenantId)->where('status', 'active')->get();
        $rows = [];

        foreach ($employees as $emp) {
            $attendances = Attendance::where('employee_id', $emp->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $rows[] = [
                'employee'    => $emp->full_name,
                'department'  => $emp->department?->name,
                'present'     => $attendances->where('status', 'present')->count(),
                'late'        => $attendances->where('status', 'late')->count(),
                'absent'      => $attendances->where('status', 'absent')->count(),
                'leave'       => $attendances->where('status', 'leave')->count(),
                'total_days'  => $attendances->count(),
            ];
        }

        return [
            'period' => $startDate->format('F Y'),
            'data'   => $rows,
        ];
    }

    public function payrollSummary(int $tenantId, int $periodId): array
    {
        $payrolls = Payroll::with('employee')
            ->where('tenant_id', $tenantId)
            ->where('period_id', $periodId)
            ->get();

        return [
            'total_gross'      => $payrolls->sum('gross_salary'),
            'total_deductions' => $payrolls->sum('total_deductions'),
            'total_tax'        => $payrolls->sum('tax_pph21'),
            'total_bpjs'       => $payrolls->sum('bpjs_kes_employee') + $payrolls->sum('bpjs_tk_employee'),
            'total_net'        => $payrolls->sum('net_salary'),
            'employee_count'   => $payrolls->count(),
            'items'            => $payrolls,
        ];
    }

    public function generatePayslipPdf(Payroll $payroll): string
    {
        $payroll->load(['employee.department', 'employee.position', 'employee.detail', 'items', 'period']);

        $pdf = Pdf::loadView('pdf.payslip', ['payroll' => $payroll]);

        $filename = "payslip_{$payroll->employee->employee_number}_{$payroll->period->month}_{$payroll->period->year}.pdf";
        $path = "payslips/{$payroll->tenant_id}/{$filename}";

        \Illuminate\Support\Facades\Storage::put($path, $pdf->output());

        return $path;
    }
}
