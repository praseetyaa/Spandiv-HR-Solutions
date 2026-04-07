<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji — {{ $payroll->employee->full_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            line-height: 1.5;
            padding: 30px;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #2B5BA8;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-left { display: table-cell; vertical-align: middle; width: 60%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 40%; }
        .company-name { font-size: 18px; font-weight: 700; color: #2B5BA8; }
        .company-detail { font-size: 10px; color: #666; margin-top: 3px; }
        .payslip-title { font-size: 14px; font-weight: 700; color: #2B5BA8; }
        .payslip-period { font-size: 11px; color: #666; margin-top: 3px; }

        /* Employee Info */
        .employee-info {
            display: table;
            width: 100%;
            background: #F0F4FA;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .info-col { display: table-cell; vertical-align: top; width: 50%; }
        .info-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .info-value { font-size: 11px; color: #1a1a2e; font-weight: 500; margin-bottom: 6px; }

        /* Salary Table */
        .salary-section { margin-bottom: 15px; }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #2B5BA8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 8px;
        }
        table.salary-table { width: 100%; border-collapse: collapse; }
        table.salary-table td { padding: 5px 8px; font-size: 11px; }
        table.salary-table td.label { color: #444; width: 60%; }
        table.salary-table td.amount { text-align: right; width: 40%; color: #1a1a2e; font-weight: 500; }
        table.salary-table tr.odd { background: #fafbfd; }

        /* Summary */
        .summary-box {
            display: table;
            width: 100%;
            background: #2B5BA8;
            color: white;
            border-radius: 6px;
            padding: 15px 20px;
            margin-top: 15px;
        }
        .summary-left { display: table-cell; vertical-align: middle; }
        .summary-right { display: table-cell; vertical-align: middle; text-align: right; }
        .summary-label { font-size: 10px; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-amount { font-size: 20px; font-weight: 700; }

        /* BPJS Section */
        .bpjs-table td { font-size: 10px; }
        .bpjs-table td.sublabel { color: #888; padding-left: 20px; }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        .confidential {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            @php
                $tenant = $payroll->employee->tenant ?? null;
                $settings = $tenant?->settings;
            @endphp
            <div class="company-name">{{ $tenant?->name ?? config('app.name') }}</div>
            <div class="company-detail">
                {{ $settings?->company_address ?? '' }}
                @if($settings?->company_phone) | {{ $settings->company_phone }} @endif
            </div>
        </div>
        <div class="header-right">
            <div class="payslip-title">SLIP GAJI</div>
            <div class="payslip-period">
                Periode: {{ $payroll->period?->month }}/{{ $payroll->period?->year }}
                <br>Tanggal Bayar: {{ $payroll->period?->pay_date ? \Carbon\Carbon::parse($payroll->period->pay_date)->format('d/m/Y') : '-' }}
            </div>
        </div>
    </div>

    {{-- Employee Info --}}
    <div class="employee-info">
        <div class="info-col">
            <div class="info-label">Nama Karyawan</div>
            <div class="info-value">{{ $payroll->employee->full_name }}</div>

            <div class="info-label">NIK / No. Karyawan</div>
            <div class="info-value">{{ $payroll->employee->employee_number }}</div>

            <div class="info-label">Departemen</div>
            <div class="info-value">{{ $payroll->employee->department?->name ?? '-' }}</div>
        </div>
        <div class="info-col">
            <div class="info-label">Jabatan</div>
            <div class="info-value">{{ $payroll->employee->position?->title ?? '-' }}</div>

            <div class="info-label">Status Pajak</div>
            <div class="info-value">{{ $payroll->employee->detail?->tax_status ?? 'TK/0' }}</div>

            <div class="info-label">Bank</div>
            <div class="info-value">{{ $payroll->employee->detail?->bank_name ?? '-' }} — {{ $payroll->employee->detail?->bank_account_name ?? '-' }}</div>
        </div>
    </div>

    @php
        $allowances = $payroll->items->where('component_type', 'allowance');
        $deductions = $payroll->items->where('component_type', 'deduction');
    @endphp

    {{-- Two Column Layout --}}
    <table style="width: 100%;"><tr>
        {{-- Left: Pendapatan --}}
        <td style="vertical-align: top; width: 49%; padding-right: 8px;">
            <div class="salary-section">
                <div class="section-title">Pendapatan</div>
                <table class="salary-table">
                    <tr class="odd">
                        <td class="label">Gaji Pokok</td>
                        <td class="amount">Rp {{ number_format($payroll->gross_salary - $payroll->total_allowances, 0, ',', '.') }}</td>
                    </tr>
                    @foreach ($allowances as $i => $item)
                        <tr class="{{ $i % 2 === 0 ? '' : 'odd' }}">
                            <td class="label">{{ $item->component_name }}</td>
                            <td class="amount">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr style="border-top: 1px solid #ddd; font-weight: 700;">
                        <td class="label" style="font-weight: 700;">Total Pendapatan</td>
                        <td class="amount" style="font-weight: 700; color: #16A34A;">Rp {{ number_format($payroll->gross_salary, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </td>

        {{-- Right: Potongan --}}
        <td style="vertical-align: top; width: 49%; padding-left: 8px;">
            <div class="salary-section">
                <div class="section-title">Potongan</div>
                <table class="salary-table">
                    @foreach ($deductions as $i => $item)
                        <tr class="{{ $i % 2 === 0 ? '' : 'odd' }}">
                            <td class="label">{{ $item->component_name }}</td>
                            <td class="amount">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="{{ $deductions->count() % 2 === 0 ? '' : 'odd' }}">
                        <td class="label">PPh 21</td>
                        <td class="amount">Rp {{ number_format($payroll->tax_pph21, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">BPJS Kesehatan (Karyawan)</td>
                        <td class="amount">Rp {{ number_format($payroll->bpjs_kes_employee, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="odd">
                        <td class="label">BPJS TK (Karyawan)</td>
                        <td class="amount">Rp {{ number_format($payroll->bpjs_tk_employee, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #ddd; font-weight: 700;">
                        <td class="label" style="font-weight: 700;">Total Potongan</td>
                        <td class="amount" style="font-weight: 700; color: #DC2626;">Rp {{ number_format($payroll->total_deductions + $payroll->tax_pph21 + $payroll->bpjs_kes_employee + $payroll->bpjs_tk_employee, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr></table>

    {{-- BPJS Employer (info only) --}}
    <div class="salary-section" style="margin-top: 10px;">
        <div class="section-title" style="font-size: 10px; color: #888;">Kontribusi Perusahaan (Tidak Dipotong)</div>
        <table class="salary-table bpjs-table">
            <tr class="odd">
                <td class="label">BPJS Kesehatan (Perusahaan)</td>
                <td class="amount">Rp {{ number_format($payroll->bpjs_kes_employer, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">BPJS TK (Perusahaan)</td>
                <td class="amount">Rp {{ number_format($payroll->bpjs_tk_employer, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- Net Salary --}}
    <div class="summary-box">
        <div class="summary-left">
            <div class="summary-label">Gaji Bersih (Take Home Pay)</div>
        </div>
        <div class="summary-right">
            <div class="summary-amount">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="confidential">RAHASIA / CONFIDENTIAL</div>
        <br>
        Dokumen ini digenerate otomatis oleh sistem HR Solutions.
        <br>
        Dicetak pada {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
    </div>
</body>
</html>
