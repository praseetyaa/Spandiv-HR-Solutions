@extends('layouts.app', ['pageTitle' => 'Slip Gaji'])

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800 m-0">Slip Gaji</h2>
            <p class="text-sm text-slate-400 mt-1 m-0">
                {{ $payroll->employee->full_name }} — Periode {{ $payroll->period?->month }}/{{ $payroll->period?->year }}
            </p>
        </div>
        <a href="{{ route('payslip.download', $payroll) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand text-white text-sm font-semibold no-underline hover:bg-brand-dark transition-all shadow-lg shadow-brand/20">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
            </svg>
            Download PDF
        </a>
    </div>

    @php
        $allowances = $payroll->items->where('component_type', 'allowance');
        $deductions = $payroll->items->where('component_type', 'deduction');
    @endphp

    {{-- Employee Card --}}
    <div class="bg-white rounded-xl border border-slate-100 p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <div class="text-xs text-slate-400 font-medium uppercase tracking-wider">Nama Karyawan</div>
                <div class="text-sm text-slate-800 font-semibold mt-1">{{ $payroll->employee->full_name }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 font-medium uppercase tracking-wider">NIK / No. Karyawan</div>
                <div class="text-sm text-slate-800 font-semibold mt-1">{{ $payroll->employee->employee_number }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 font-medium uppercase tracking-wider">Departemen</div>
                <div class="text-sm text-slate-800 font-semibold mt-1">{{ $payroll->employee->department?->name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 font-medium uppercase tracking-wider">Jabatan</div>
                <div class="text-sm text-slate-800 font-semibold mt-1">{{ $payroll->employee->position?->title ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Salary Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Pendapatan --}}
        <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-50 bg-emerald-50/50">
                <h3 class="text-sm font-bold text-emerald-700 m-0 uppercase tracking-wider">Pendapatan</h3>
            </div>
            <div class="px-6 py-4">
                <table class="w-full">
                    <tbody class="text-sm">
                        <tr class="border-b border-slate-50">
                            <td class="py-2.5 text-slate-600">Gaji Pokok</td>
                            <td class="py-2.5 text-right font-medium text-slate-800">Rp {{ number_format($payroll->gross_salary - $payroll->total_allowances, 0, ',', '.') }}</td>
                        </tr>
                        @foreach ($allowances as $item)
                            <tr class="border-b border-slate-50">
                                <td class="py-2.5 text-slate-600">{{ $item->component_name }}</td>
                                <td class="py-2.5 text-right font-medium text-slate-800">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-emerald-100">
                            <td class="py-3 font-bold text-emerald-700">Total Pendapatan</td>
                            <td class="py-3 text-right font-bold text-emerald-700 text-lg">Rp {{ number_format($payroll->gross_salary, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Potongan --}}
        <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-50 bg-red-50/50">
                <h3 class="text-sm font-bold text-red-700 m-0 uppercase tracking-wider">Potongan</h3>
            </div>
            <div class="px-6 py-4">
                <table class="w-full">
                    <tbody class="text-sm">
                        @foreach ($deductions as $item)
                            <tr class="border-b border-slate-50">
                                <td class="py-2.5 text-slate-600">{{ $item->component_name }}</td>
                                <td class="py-2.5 text-right font-medium text-slate-800">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="border-b border-slate-50">
                            <td class="py-2.5 text-slate-600">PPh 21</td>
                            <td class="py-2.5 text-right font-medium text-slate-800">Rp {{ number_format($payroll->tax_pph21, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-slate-50">
                            <td class="py-2.5 text-slate-600">BPJS Kesehatan</td>
                            <td class="py-2.5 text-right font-medium text-slate-800">Rp {{ number_format($payroll->bpjs_kes_employee, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-slate-50">
                            <td class="py-2.5 text-slate-600">BPJS Ketenagakerjaan</td>
                            <td class="py-2.5 text-right font-medium text-slate-800">Rp {{ number_format($payroll->bpjs_tk_employee, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-red-100">
                            <td class="py-3 font-bold text-red-700">Total Potongan</td>
                            <td class="py-3 text-right font-bold text-red-700 text-lg">Rp {{ number_format($payroll->total_deductions + $payroll->tax_pph21 + $payroll->bpjs_kes_employee + $payroll->bpjs_tk_employee, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Net Salary --}}
    <div class="bg-gradient-to-r from-brand to-brand-dark rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-white/70">Gaji Bersih (Take Home Pay)</div>
                <div class="text-3xl font-bold mt-1">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</div>
            </div>
            <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/>
                </svg>
            </div>
        </div>
    </div>
@endsection
