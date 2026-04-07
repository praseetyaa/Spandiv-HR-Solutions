<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalaryComponent;
use App\Models\PayrollPeriod;
use App\Models\Payroll;
use App\Models\PayrollItem;

class PayrollCalculatorService
{
    public function __construct(
        private PPh21CalculatorService $pph21,
        private BPJSCalculatorService  $bpjs,
    ) {}

    public function calculateForEmployee(Employee $employee, PayrollPeriod $period): array
    {
        $salary = EmployeeSalary::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->latest('effective_date')
            ->first();

        if (!$salary) {
            return ['error' => 'No active salary record found'];
        }

        $basicSalary = (float) $salary->basic_salary;

        // Gather salary components
        $components = EmployeeSalaryComponent::where('employee_id', $employee->id)
            ->where('effective_date', '<=', $period->pay_date)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $period->pay_date))
            ->with('component')
            ->get();

        $totalAllowances = 0;
        $totalDeductions = 0;
        $items = [];

        foreach ($components as $esc) {
            $comp = $esc->component;
            $amount = (float) $esc->amount;

            $items[] = [
                'component_id'   => $comp->id,
                'component_name' => $comp->name,
                'component_type' => $comp->type,
                'amount'         => $amount,
            ];

            if ($comp->type === 'allowance') {
                $totalAllowances += $amount;
            } else {
                $totalDeductions += $amount;
            }
        }

        $grossSalary = $basicSalary + $totalAllowances;

        // BPJS
        $bpjsResult = $this->bpjs->calculate($basicSalary);

        // PPh21
        $taxStatus = $employee->detail?->tax_status ?? 'TK/0';
        $pph21Result = $this->pph21->calculate($grossSalary, $taxStatus);

        $bpjsKesEmployee = $bpjsResult['bpjs_kes_employee'];
        $bpjsTkEmployee  = $bpjsResult['bpjs_tk_jht_employee'] + $bpjsResult['bpjs_tk_jp_employee'];
        $bpjsKesEmployer = $bpjsResult['bpjs_kes_employer'];
        $bpjsTkEmployer  = $bpjsResult['bpjs_tk_jht_employer'] + $bpjsResult['bpjs_tk_jp_employer']
                         + $bpjsResult['bpjs_tk_jkk_employer'] + $bpjsResult['bpjs_tk_jkm_employer'];

        $taxPph21 = $pph21Result['pph21_monthly'];

        $netSalary = $grossSalary - $totalDeductions - $bpjsKesEmployee - $bpjsTkEmployee - $taxPph21;

        return [
            'employee_id'         => $employee->id,
            'basic_salary'        => $basicSalary,
            'gross_salary'        => $grossSalary,
            'total_allowances'    => $totalAllowances,
            'total_deductions'    => $totalDeductions,
            'tax_pph21'           => $taxPph21,
            'bpjs_kes_employee'   => $bpjsKesEmployee,
            'bpjs_kes_employer'   => $bpjsKesEmployer,
            'bpjs_tk_employee'    => $bpjsTkEmployee,
            'bpjs_tk_employer'    => $bpjsTkEmployer,
            'net_salary'          => $netSalary,
            'items'               => $items,
            'pph21_detail'        => $pph21Result,
            'bpjs_detail'         => $bpjsResult,
        ];
    }

    public function processPayroll(PayrollPeriod $period): int
    {
        $employees = Employee::where('tenant_id', $period->tenant_id)
            ->where('status', 'active')
            ->get();

        $count = 0;

        foreach ($employees as $employee) {
            $result = $this->calculateForEmployee($employee, $period);

            if (isset($result['error'])) continue;

            $payroll = Payroll::updateOrCreate(
                ['period_id' => $period->id, 'employee_id' => $employee->id],
                [
                    'tenant_id'          => $period->tenant_id,
                    'gross_salary'       => $result['gross_salary'],
                    'total_allowances'   => $result['total_allowances'],
                    'total_deductions'   => $result['total_deductions'],
                    'tax_pph21'          => $result['tax_pph21'],
                    'bpjs_kes_employee'  => $result['bpjs_kes_employee'],
                    'bpjs_kes_employer'  => $result['bpjs_kes_employer'],
                    'bpjs_tk_employee'   => $result['bpjs_tk_employee'],
                    'bpjs_tk_employer'   => $result['bpjs_tk_employer'],
                    'net_salary'         => $result['net_salary'],
                    'status'             => 'draft',
                ]
            );

            // Save payroll items
            $payroll->items()->delete();
            foreach ($result['items'] as $item) {
                PayrollItem::create([
                    'payroll_id'     => $payroll->id,
                    'component_id'   => $item['component_id'],
                    'component_name' => $item['component_name'],
                    'component_type' => $item['component_type'],
                    'amount'         => $item['amount'],
                ]);
            }

            $count++;
        }

        return $count;
    }
}
