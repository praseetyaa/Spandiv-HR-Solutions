<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Kebijakan Perusahaan</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola kebijakan, versi dokumen, dan acknowledgment karyawan</p>
        </div>
        <button wire:click="openPolicyForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Kebijakan
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kebijakan..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
        <select wire:model.live="categoryFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Kategori</option>
            <option value="hr">👥 HR</option>
            <option value="legal">⚖️ Legal</option>
            <option value="safety">🦺 K3</option>
            <option value="it">💻 IT</option>
            <option value="finance">💰 Finance</option>
        </select>
    </div>

    {{-- Split layout: list + detail --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Policy List --}}
        <div class="lg:col-span-3">
            <x-ui.card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Kebijakan</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Kategori</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Versi</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                        </tr></thead>
                        <tbody>
                            @forelse($this->policies as $policy)
                                <tr wire:click="selectPolicy({{ $policy->id }})" class="border-b border-slate-50 hover:bg-slate-50/50 cursor-pointer {{ $selectedPolicyId === $policy->id ? 'bg-brand-50' : '' }}">
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-900">{{ $policy->title }}</div>
                                        @if($policy->code)<div class="text-xs text-slate-400">{{ $policy->code }}</div>@endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-500">{{ $policy->category_label }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-xs font-bold text-slate-600">{{ $policy->versions_count }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($policy->is_active)
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">AKTIF</span>
                                        @else
                                            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">NON-AKTIF</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button wire:click.stop="openPolicyForm({{ $policy->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                            <button wire:click.stop="toggleActive({{ $policy->id }})" class="p-1.5 rounded-lg border-none bg-amber-50 text-amber-500 cursor-pointer hover:bg-amber-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></button>
                                            <button wire:click.stop="deletePolicy({{ $policy->id }})" wire:confirm="Hapus kebijakan ini?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">📜</div>
                                    <div class="font-medium text-slate-600 mb-1">Belum Ada Kebijakan</div>
                                    <div class="text-sm">Buat kebijakan pertama perusahaan Anda.</div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>

        {{-- Detail Panel --}}
        <div class="lg:col-span-2">
            <x-ui.card>
                @if($this->selectedPolicy)
                    <div class="p-1">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="m-0 text-lg font-bold text-slate-900">{{ $this->selectedPolicy->title }}</h3>
                                <span class="text-xs text-slate-400">{{ $this->selectedPolicy->category_label }}</span>
                            </div>
                            <button wire:click="openVersionForm" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand text-white text-xs font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Versi Baru</button>
                        </div>

                        @if($this->selectedPolicy->description)
                            <p class="text-sm text-slate-500 mb-4">{{ $this->selectedPolicy->description }}</p>
                        @endif

                        <div class="text-xs font-semibold text-slate-500 uppercase mb-2">Riwayat Versi</div>
                        <div class="space-y-2">
                            @forelse($this->selectedPolicy->versions as $version)
                                <div class="p-3 rounded-xl {{ $version->is_current ? 'bg-brand-50 border border-brand-200' : 'bg-slate-50' }}">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-bold text-slate-900">v{{ $version->version_number }}</span>
                                        @if($version->is_current)
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">CURRENT</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-500">Berlaku: {{ $version->effective_date->format('d M Y') }}</div>
                                    <div class="text-xs text-slate-400 mt-1 line-clamp-2">{{ Str::limit($version->content, 100) }}</div>
                                </div>
                            @empty
                                <div class="text-sm text-slate-400 text-center py-4">Belum ada versi.</div>
                            @endforelse
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-3xl mb-2">👆</div>
                        <div class="text-sm text-slate-400">Pilih kebijakan untuk detail</div>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>

    {{-- Policy Form Modal --}}
    @if($showPolicyForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showPolicyForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[520px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingPolicyId ? 'Edit' : 'Tambah' }} Kebijakan</h3></div>
                <form wire:submit="savePolicy" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Judul <span class="text-danger">*</span></label><input type="text" wire:model="policyTitle" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('policyTitle')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kategori</label><select wire:model="policyCategory" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="hr">👥 HR</option><option value="legal">⚖️ Legal</option><option value="safety">🦺 K3</option><option value="it">💻 IT</option><option value="finance">💰 Finance</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kode</label><input type="text" wire:model="policyCode" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="e.g. POL-001"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi</label><textarea wire:model="policyDescription" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="policyRequiresAck" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Wajib Acknowledgment</span></label>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showPolicyForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif

    {{-- Version Form Modal --}}
    @if($showVersionForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showVersionForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[560px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Buat Versi Baru</h3></div>
                <form wire:submit="saveVersion" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal Efektif <span class="text-danger">*</span></label><input type="date" wire:model="versionEffectiveDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Konten <span class="text-danger">*</span></label><textarea wire:model="versionContent" rows="8" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea>@error('versionContent')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showVersionForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Publish Versi</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
