<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">Jadwal Interview</h2>
        <div class="flex gap-2">
            <select wire:model.live="filterStatus" class="rounded-lg bg-white/5 border border-white/10 text-sm text-white px-3 py-2">
                <option value="">Semua Status</option>
                <option value="scheduled">Terjadwal</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
            <button wire:click="$set('showModal', true)" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-medium hover:shadow-lg hover:shadow-cyan-500/30 transition">
                + Jadwalkan
            </button>
        </div>
    </div>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-xs text-slate-400 uppercase border-b border-white/10">
                        <th class="pb-3 font-semibold">Kandidat</th>
                        <th class="pb-3 font-semibold">Tipe</th>
                        <th class="pb-3 font-semibold">Jadwal</th>
                        <th class="pb-3 font-semibold">Interviewer</th>
                        <th class="pb-3 font-semibold">Lokasi</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($interviews as $iv)
                        <tr class="hover:bg-white/5 transition">
                            <td class="py-3 text-white">{{ $iv->candidate?->name }}</td>
                            <td class="py-3"><x-ui.badge variant="{{ $iv->interview_type === 'hr' ? 'info' : ($iv->interview_type === 'technical' ? 'warning' : 'primary') }}">{{ ucfirst($iv->interview_type) }}</x-ui.badge></td>
                            <td class="py-3 text-slate-300">{{ $iv->scheduled_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 text-slate-300">{{ $iv->interviewer_name }}</td>
                            <td class="py-3 text-slate-400">{{ $iv->location ?? '-' }}</td>
                            <td class="py-3"><x-ui.badge variant="{{ $iv->status === 'scheduled' ? 'info' : ($iv->status === 'completed' ? 'success' : 'danger') }}">{{ ucfirst($iv->status) }}</x-ui.badge></td>
                            <td class="py-3">
                                <button wire:click="edit({{ $iv->id }})" class="text-cyan-400 hover:text-cyan-300 text-xs transition">Edit</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-500">Belum ada jadwal interview</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $interviews->links() }}</div>
    </x-ui.card>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="w-full max-w-lg mx-4 rounded-2xl bg-slate-800 border border-white/10 p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-white mb-4">{{ $editingId ? 'Edit' : 'Tambah' }} Jadwal Interview</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-slate-400">Kandidat</label>
                        <select wire:model="candidate_id" class="w-full mt-1 rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2">
                            <option value="">Pilih Kandidat</option>
                            @foreach($candidates as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-slate-400">Tipe</label>
                            <select wire:model="interview_type" class="w-full mt-1 rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2">
                                <option value="hr">HR</option>
                                <option value="technical">Technical</option>
                                <option value="user">User</option>
                                <option value="final">Final</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-slate-400">Jadwal</label>
                            <input type="datetime-local" wire:model="scheduled_at" class="w-full mt-1 rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm text-slate-400">Interviewer</label>
                        <input type="text" wire:model="interviewer_name" class="w-full mt-1 rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm text-slate-400">Lokasi</label>
                        <input type="text" wire:model="location" class="w-full mt-1 rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm text-slate-400">Catatan</label>
                        <textarea wire:model="notes" rows="2" class="w-full mt-1 rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 rounded-lg bg-white/10 text-white text-sm hover:bg-white/20 transition">Batal</button>
                    <button wire:click="save" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-medium hover:shadow-lg transition">Simpan</button>
                </div>
            </div>
        </div>
    @endif
</div>
