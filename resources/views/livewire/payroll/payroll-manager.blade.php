<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Penggajian</h2><p class="text-sm text-slate-500 mt-1 mb-0">Kelola periode dan slip gaji karyawan</p></div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Buat Periode</button>
    </div>
    <div class="flex gap-6">
        <div class="w-[320px] shrink-0">
            <div class="space-y-2">
                @forelse($this->periods as $period)
                    <div wire:click="selectPeriod({{ $period->id }})" class="bg-white rounded-xl p-4 border cursor-pointer transition-all duration-200 hover:shadow-md {{ $selectedPeriodId === $period->id ? 'border-brand shadow-md' : 'border-slate-100' }}">
                        <div class="flex items-center justify-between mb-1"><h4 class="m-0 text-sm font-bold text-slate-900">{{ $period->period_label }}</h4><span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $period->status === 'completed' ? 'text-emerald-600 bg-emerald-50' : ($period->status === 'processing' ? 'text-blue-600 bg-blue-50' : 'text-slate-500 bg-slate-100') }}">{{ $period->status_label }}</span></div>
                        <div class="flex items-center gap-3 text-xs text-slate-400"><span>💰 {{ $period->payrolls_count }} slip</span><span>📅 {{ $period->pay_date->format('d M Y') }}</span></div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-sm">Belum ada periode.</div>
                @endforelse
            </div>
        </div>
        <div class="flex-1 min-w-0">
            @if($this->selectedPeriod)
                <x-ui.card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="m-0 text-lg font-bold text-slate-900">{{ $this->selectedPeriod->period_label }}</h3>
                        <button wire:click="delete({{ $this->selectedPeriod->id }})" wire:confirm="Hapus periode ini?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                    </div>
                    <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100">
                        <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
                        <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Gaji Pokok</th>
                        <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Tunjangan</th>
                        <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Potongan</th>
                        <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Gaji Bersih</th>
                    </tr></thead><tbody>
                        @forelse($this->payrolls as $p)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                                <td class="py-3 px-4"><div class="font-medium text-slate-900">{{ $p->employee->full_name }}</div><div class="text-xs text-slate-400">{{ $p->employee->department?->name ?? '-' }}</div></td>
                                <td class="py-3 px-4 text-right font-mono">{{ number_format($p->gross_salary) }}</td>
                                <td class="py-3 px-4 text-right font-mono text-emerald-600">+{{ number_format($p->total_allowances) }}</td>
                                <td class="py-3 px-4 text-right font-mono text-red-500">-{{ number_format($p->total_deductions) }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-brand">{{ number_format($p->net_salary) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-12 text-center text-slate-400">Belum ada data slip gaji.</td></tr>
                        @endforelse
                    </tbody></table></div>
                </x-ui.card>
            @else
                <div class="flex items-center justify-center h-64 bg-white rounded-xl border border-slate-100"><p class="text-slate-400 text-sm">← Pilih periode untuk melihat slip gaji</p></div>
            @endif
        </div>
    </div>
    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[420px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Buat Periode Payroll</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Bulan</label><select wire:model="month" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">@for($m=1;$m<=12;$m++)<option value="{{ $m }}">{{ ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'][$m] }}</option>@endfor</select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tahun</label><input type="number" wire:model="year" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal Bayar <span class="text-danger">*</span></label><input type="date" wire:model="payDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
