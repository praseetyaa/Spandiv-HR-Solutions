<?php

namespace App\Services;

class PPh21CalculatorService
{
    public function calculate(float $grossMonthly, string $taxStatus = 'TK/0', bool $isEmployee = true): array
    {
        $biayaJabatan = $isEmployee
            ? min($grossMonthly * 0.05, config('hr.biaya_jabatan.max_month'))
            : 0;

        $netoMonthly = $grossMonthly - $biayaJabatan;
        $netoAnnual  = $netoMonthly * 12;
        $ptkp        = $this->getPtkp($taxStatus);
        $pkp         = max(0, $netoAnnual - $ptkp);
        $pph21Annual = $this->applyProgressiveTax($pkp);
        $pph21Monthly = round($pph21Annual / 12);

        return [
            'gross_monthly'  => $grossMonthly,
            'biaya_jabatan'  => $biayaJabatan,
            'neto_monthly'   => $netoMonthly,
            'neto_annual'    => $netoAnnual,
            'ptkp'           => $ptkp,
            'pkp'            => $pkp,
            'pph21_annual'   => $pph21Annual,
            'pph21_monthly'  => $pph21Monthly,
            'tax_status'     => $taxStatus,
            'effective_rate' => $grossMonthly > 0
                ? round(($pph21Monthly / $grossMonthly) * 100, 2)
                : 0,
        ];
    }

    private function getPtkp(string $taxStatus): int
    {
        return config('hr.ptkp')[$taxStatus] ?? config('hr.ptkp.TK/0');
    }

    private function applyProgressiveTax(float $pkp): float
    {
        $tax = 0;
        foreach (config('hr.pph21_brackets') as $bracket) {
            if ($pkp <= 0) break;
            $upper   = $bracket['max'] ?? PHP_INT_MAX;
            $lower   = $bracket['min'];
            $rate    = $bracket['rate'] / 100;
            $taxable = min($pkp, $upper - $lower);
            $tax    += $taxable * $rate;
            $pkp    -= $taxable;
        }
        return $tax;
    }

    public function calculateTER(float $grossMonthly, string $taxStatus): float
    {
        return $this->calculate($grossMonthly, $taxStatus)['pph21_monthly'];
    }
}
