<?php

namespace App\Services;

class BPJSCalculatorService
{
    public function calculate(float $basicSalary): array
    {
        $cfg = config('hr.bpjs');

        // BPJS Kesehatan
        $kesBase     = min($basicSalary, $cfg['kesehatan']['max_salary']);
        $kesEmployee = $kesBase * ($cfg['kesehatan']['employee'] / 100);
        $kesEmployer = $kesBase * ($cfg['kesehatan']['employer'] / 100);

        // BPJS Ketenagakerjaan
        $tk = $cfg['ketenagakerjaan'];

        $jhtEmployee = $basicSalary * ($tk['jht_employee'] / 100);
        $jhtEmployer = $basicSalary * ($tk['jht_employer'] / 100);

        $jpBase      = min($basicSalary, $tk['max_jp_salary']);
        $jpEmployee  = $jpBase * ($tk['jp_employee'] / 100);
        $jpEmployer  = $jpBase * ($tk['jp_employer'] / 100);

        $jkk = $basicSalary * ($tk['jkk_employer'] / 100);
        $jkm = $basicSalary * ($tk['jkm_employer'] / 100);

        $totalEmployee = $kesEmployee + $jhtEmployee + $jpEmployee;
        $totalEmployer = $kesEmployer + $jhtEmployer + $jpEmployer + $jkk + $jkm;

        return [
            'bpjs_kes_employee'        => round($kesEmployee),
            'bpjs_kes_employer'        => round($kesEmployer),
            'bpjs_tk_jht_employee'     => round($jhtEmployee),
            'bpjs_tk_jht_employer'     => round($jhtEmployer),
            'bpjs_tk_jp_employee'      => round($jpEmployee),
            'bpjs_tk_jp_employer'      => round($jpEmployer),
            'bpjs_tk_jkk_employer'     => round($jkk),
            'bpjs_tk_jkm_employer'     => round($jkm),
            'total_employee_deduction' => round($totalEmployee),
            'total_employer_cost'      => round($totalEmployer),
        ];
    }
}
