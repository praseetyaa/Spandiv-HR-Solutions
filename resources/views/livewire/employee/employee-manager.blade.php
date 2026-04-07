<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Data Karyawan</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola seluruh data karyawan perusahaan</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Karyawan
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-slate-900">{{ $this->employees->count() }}</div><div class="text-xs text-slate-400 mt-1">Total Karyawan</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-emerald-600">{{ $this->employees->where('status', 'active')->count() }}</div><div class="text-xs text-slate-400 mt-1">Aktif</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-amber-600">{{ $this->employees->where('employment_type', 'contract')->count() }}</div><div class="text-xs text-slate-400 mt-1">Kontrak</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-red-500">{{ $this->employees->where('status', 'terminated')->count() + $this->employees->where('status', 'resigned')->count() }}</div><div class="text-xs text-slate-400 mt-1">Non-Aktif</div></div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[200px] max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / NIP / email..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="departmentFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Departemen</option>@foreach($this->departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Status</option><option value="active">Aktif</option><option value="inactive">Non-Aktif</option><option value="terminated">Terminated</option><option value="resigned">Resign</option></select>
    </div>

    {{-- Split Layout --}}
    <div class="flex gap-6">
        {{-- Employee Table --}}
        <div class="flex-1 min-w-0">
            <x-ui.card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Departemen</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Jabatan</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                        </tr></thead>
                        <tbody>
                            @forelse($this->employees as $emp)
                                <tr wire:click="selectEmployee({{ $emp->id }})" class="border-b border-slate-50 hover:bg-slate-50/50 cursor-pointer {{ $selectedId === $emp->id ? 'bg-brand-50' : '' }}">
                                    <td class="py-3 px-4"><div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand to-[#3468B8] flex items-center justify-center text-xs font-bold text-white">{{ $emp->initials }}</div><div><div class="font-medium text-slate-900">{{ $emp->full_name }}</div><div class="text-xs text-slate-400">{{ $emp->employee_number }}</div></div></div></td>
                                    <td class="py-3 px-4 text-slate-600">{{ $emp->department?->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-slate-600">{{ $emp->jobPosition?->title ?? '-' }}</td>
                                    <td class="py-3 px-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $emp->status === 'active' ? 'text-emerald-600 bg-emerald-50' : 'text-red-500 bg-red-50' }}">{{ strtoupper($emp->status) }}</span></td>
                                    <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1">
                                        <button wire:click.stop="openForm({{ $emp->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                        <button wire:click.stop="delete({{ $emp->id }})" wire:confirm="Hapus karyawan ini?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                    </div></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-12 text-center text-slate-400">Belum ada data karyawan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>

        {{-- Detail Panel --}}
        @if($this->selected)
            <div class="w-[340px] shrink-0">
                <x-ui.card>
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-brand to-[#3468B8] flex items-center justify-center text-xl font-bold text-white mx-auto mb-3">{{ $this->selected->initials }}</div>
                        <h3 class="m-0 text-lg font-bold text-slate-900">{{ $this->selected->full_name }}</h3>
                        <p class="text-sm text-slate-400 mt-0.5 mb-0">{{ $this->selected->jobPosition?->title ?? '-' }}</p>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-slate-400">NIP</span><span class="font-medium text-slate-900">{{ $this->selected->employee_number }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">Email</span><span class="font-medium text-slate-900">{{ $this->selected->email ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">Telepon</span><span class="font-medium text-slate-900">{{ $this->selected->phone ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">Departemen</span><span class="font-medium text-slate-900">{{ $this->selected->department?->name ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">Tipe</span><span class="font-medium text-slate-900">{{ ucfirst($this->selected->employment_type) }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">Bergabung</span><span class="font-medium text-slate-900">{{ $this->selected->join_date->format('d M Y') }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">Atasan</span><span class="font-medium text-slate-900">{{ $this->selected->manager?->full_name ?? '-' }}</span></div>
                    </div>
                </x-ui.card>
            </div>
        @endif
    </div>

    {{-- Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[600px] max-h-[85vh] overflow-y-auto">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Tambah' }} Karyawan</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama Depan <span class="text-danger">*</span></label><input type="text" wire:model="firstName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('firstName')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama Belakang</label><input type="text" wire:model="lastName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">NIP <span class="text-danger">*</span></label><input type="text" wire:model="employeeNumber" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal Bergabung <span class="text-danger">*</span></label><input type="date" wire:model="joinDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Email</label><input type="email" wire:model="email" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Telepon</label><input type="text" wire:model="phone" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Departemen <span class="text-danger">*</span></label><select wire:model="departmentId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Jabatan <span class="text-danger">*</span></label><select wire:model="positionId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->positions as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach</select></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe</label><select wire:model="employmentType" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="permanent">Tetap</option><option value="contract">Kontrak</option><option value="internship">Magang</option><option value="freelance">Freelance</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Status</label><select wire:model="status" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="active">Aktif</option><option value="inactive">Non-Aktif</option><option value="terminated">Terminated</option><option value="resigned">Resign</option></select></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
