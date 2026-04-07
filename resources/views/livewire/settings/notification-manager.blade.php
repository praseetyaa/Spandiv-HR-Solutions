<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Template Notifikasi</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola template notifikasi per event dan channel</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Template
        </button>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari event..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="channelFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Channel</option><option value="email">📧 Email</option><option value="whatsapp">💬 WhatsApp</option><option value="in_app">🔔 In-App</option></select>
    </div>

    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-slate-100">
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Event</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Channel</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Subject</th>
                    <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
                    <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($this->templates as $tpl)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 font-mono text-xs">{{ $tpl->event_key }}</div></td>
                            <td class="py-3 px-4"><span class="text-xs">{{ $tpl->channel_label }}</span></td>
                            <td class="py-3 px-4 text-slate-500 text-xs">{{ $tpl->subject ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($tpl->is_active)<span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">AKTIF</span>
                                @else<span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">NON-AKTIF</span>@endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="toggleActive({{ $tpl->id }})" class="p-1.5 rounded-lg border-none bg-amber-50 text-amber-500 cursor-pointer hover:bg-amber-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></button>
                                    <button wire:click="openForm({{ $tpl->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                    <button wire:click="delete({{ $tpl->id }})" wire:confirm="Hapus template?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-12 text-center text-slate-400"><div class="text-3xl mb-2">🔔</div><div class="font-medium text-slate-600 mb-1">Belum Ada Template</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[520px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Tambah' }} Template</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Event Key <span class="text-danger">*</span></label><input type="text" wire:model="eventKey" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none font-mono" placeholder="e.g. leave.approved"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Channel</label><select wire:model="channel" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="email">📧 Email</option><option value="whatsapp">💬 WhatsApp</option><option value="in_app">🔔 In-App</option></select></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Subject</label><input type="text" wire:model="subject" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Body Template <span class="text-danger">*</span></label><textarea wire:model="bodyTemplate" rows="5" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-mono"></textarea></div>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="isActive" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Aktif</span></label>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
