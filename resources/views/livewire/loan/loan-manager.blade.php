<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Pinjaman Karyawan</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola pinjaman dan cicilan karyawan</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Pinjaman
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="p-4 rounded-xl bg-white border border-slate-100">
            <div class="text-2xl font-extrabold text-slate-900">{{ $this->loans->count() }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Total Pinjaman</div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-100">
            <div class="text-2xl font-extrabold text-slate-900">{{ $this->loans->where('status', 'active')->count() }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Aktif</div>
        </div>
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-100">
            <div class="text-2xl font-extrabold text-amber-600">Rp {{ number_format($this->totalOutstanding) }}</div>
            <div class="text-xs text-amber-600 mt-0.5">Outstanding</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative flex-1 min-w-[200px] max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Status</option><option value="pending">Pending</option><option value="active">Aktif</option><option value="completed">Lunas</option><option value="cancelled">Dibatalkan</option></select>
    </div>

    {{-- Loan Cards --}}
    <div class="flex flex-col gap-4">
        @forelse($this->loans as $loan)
            <x-ui.card>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-xl shrink-0">💰</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-base font-bold text-slate-900">{{ $loan->employee->full_name }}</span>
                            @php
                                $loanStatusType = match($loan->status) {
                                    'pending' => 'warning', 'active' => 'success', 'completed' => 'info', 'cancelled' => 'danger', default => 'default',
                                };
                            @endphp
                            <x-ui.badge :type="$loanStatusType" size="xs">{{ ucfirst($loan->status) }}</x-ui.badge>
                        </div>
                        <div class="text-sm text-slate-500">Rp {{ number_format($loan->loan_amount) }} · {{ $loan->installment_months }}x · Rp {{ number_format($loan->monthly_deduction) }}/bln</div>
                    </div>

                    <div class="text-right shrink-0 hidden sm:block">
                        <div class="text-xl font-extrabold text-slate-900">{{ $loan->progress_percent }}%</div>
                        <div class="text-xs text-slate-400">Terbayar</div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        @if($loan->status === 'pending')
                            <button wire:click="approveLoan({{ $loan->id }})" class="p-2 rounded-lg border-none bg-green-50 text-green-600 cursor-pointer hover:bg-green-100 transition-colors" title="Approve">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                        @endif
                        <button wire:click="openForm({{ $loan->id }})" class="p-2 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                        <button wire:click="deleteLoan({{ $loan->id }})" wire:confirm="Hapus?" class="p-2 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-slate-500">Sisa: Rp {{ number_format($loan->remaining_amount) }}</span>
                        <span class="text-xs text-slate-400">Mulai: {{ $loan->start_date->format('d M Y') }}</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $loan->progress_percent >= 100 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ min($loan->progress_percent, 100) }}%"></div>
                    </div>
                </div>
            </x-ui.card>
        @empty
            <x-ui.card>
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 mx-auto mb-4 flex items-center justify-center text-2xl">💰</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Pinjaman</h3>
                    <p class="text-sm text-slate-400 mb-4">Buat pinjaman pertama.</p>
                    <button wire:click="openForm" class="px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer">Buat Pinjaman</button>
                </div>
            </x-ui.card>
        @endforelse
    </div>

    {{-- Loan Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Buat' }} Pinjaman</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="employeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select>@error('employeeId')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Jumlah Pinjaman <span class="text-danger">*</span></label><input type="number" wire:model="loanAmount" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" step="0.01"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Cicilan (bulan) <span class="text-danger">*</span></label><input type="number" wire:model="installmentMonths" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" min="1"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal Mulai <span class="text-danger">*</span></label><input type="date" wire:model="startDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Catatan</label><textarea wire:model="notes" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    @if($loanAmount > 0 && $installmentMonths > 0)
                        <div class="p-3 bg-brand-50 rounded-lg text-sm text-brand-700">Cicilan: <strong>Rp {{ number_format(round($loanAmount / $installmentMonths)) }}/bulan</strong></div>
                    @endif
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
