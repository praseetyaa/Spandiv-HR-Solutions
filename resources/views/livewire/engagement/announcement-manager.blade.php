<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Pengumuman</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Buat dan kelola pengumuman perusahaan</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Pengumuman
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-slate-100">
            <div class="text-2xl font-bold text-slate-900">{{ $this->stats['total'] }}</div>
            <div class="text-xs text-slate-400">Total Pengumuman</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-emerald-100">
            <div class="text-2xl font-bold text-emerald-600">{{ $this->stats['published'] }}</div>
            <div class="text-xs text-slate-400">Published</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-red-100">
            <div class="text-2xl font-bold text-red-600">{{ $this->stats['urgent'] }}</div>
            <div class="text-xs text-slate-400">Urgent</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari pengumuman..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
        <select wire:model.live="priorityFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Prioritas</option>
            <option value="low">Rendah</option>
            <option value="normal">Normal</option>
            <option value="high">Penting</option>
            <option value="urgent">🔴 Urgent</option>
        </select>
    </div>

    {{-- Announcement Cards --}}
    <div class="space-y-4">
        @forelse($this->announcements as $ann)
            @php
                $priorColors = ['low' => 'border-l-slate-300', 'normal' => 'border-l-blue-400', 'high' => 'border-l-amber-400', 'urgent' => 'border-l-red-500'];
                $priorBadges = ['low' => 'bg-slate-100 text-slate-600', 'normal' => 'bg-blue-100 text-blue-600', 'high' => 'bg-amber-100 text-amber-700', 'urgent' => 'bg-red-100 text-red-700'];
            @endphp
            <div class="bg-white rounded-xl p-5 border border-slate-100 border-l-4 {{ $priorColors[$ann->priority] ?? '' }} hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="m-0 text-[15px] font-bold text-slate-900">{{ $ann->title }}</h4>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $priorBadges[$ann->priority] ?? '' }}">{{ strtoupper($ann->priority_label) }}</span>
                            @if($ann->is_published)
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">PUBLISHED</span>
                            @else
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">DRAFT</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-600 mb-0 line-clamp-2">{{ $ann->content }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-3">
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <span>📅 {{ $ann->publish_at->format('d M Y H:i') }}</span>
                        @if($ann->expires_at)<span>⏱️ s.d. {{ $ann->expires_at->format('d M Y') }}</span>@endif
                        @if($ann->creator)<span>✍️ {{ $ann->creator->name }}</span>@endif
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="togglePublish({{ $ann->id }})" class="px-3 py-1.5 text-xs font-semibold rounded-lg border-none cursor-pointer transition-colors {{ $ann->is_published ? 'bg-slate-100 text-slate-500 hover:bg-slate-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                            {{ $ann->is_published ? 'Unpublish' : '🚀 Publish' }}
                        </button>
                        <button wire:click="openForm({{ $ann->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                        <button wire:click="delete({{ $ann->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-slate-400">
                <div class="text-3xl mb-2">📢</div>
                <div class="font-medium text-slate-600 mb-1">Belum Ada Pengumuman</div>
                <div class="text-sm">Buat pengumuman pertama untuk tim Anda.</div>
                <button wire:click="openForm" class="mt-3 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">Buat Pengumuman</button>
            </div>
        @endforelse
    </div>

    {{-- Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[560px] max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Buat' }} Pengumuman</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Judul <span class="text-danger">*</span></label><input type="text" wire:model="announcementTitle" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('announcementTitle')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Konten <span class="text-danger">*</span></label><textarea wire:model="announcementContent" rows="5" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea>@error('announcementContent')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Prioritas</label><select wire:model="announcementPriority" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="low">Rendah</option><option value="normal">Normal</option><option value="high">Penting</option><option value="urgent">🔴 Urgent</option></select></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Publish At <span class="text-danger">*</span></label><input type="datetime-local" wire:model="publishAt" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Expires At</label><input type="datetime-local" wire:model="expiresAt" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
