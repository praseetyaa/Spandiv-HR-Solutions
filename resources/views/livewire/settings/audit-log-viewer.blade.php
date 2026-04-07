<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Audit Log</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Riwayat aktivitas sistem dan perubahan data</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari model/aksi..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="actionFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Aksi</option><option value="created">🟢 Created</option><option value="updated">🔵 Updated</option><option value="deleted">🔴 Deleted</option><option value="login">🔑 Login</option></select>
        <select wire:model.live="userFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua User</option>@foreach($this->users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select>
        <input type="date" wire:model.live="dateFrom" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white" placeholder="Dari">
        <input type="date" wire:model.live="dateTo" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white" placeholder="Sampai">
    </div>

    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-slate-100">
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Waktu</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">User</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Model</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">IP</th>
                </tr></thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="py-3 px-4 text-xs text-slate-500 whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4"><div class="text-sm font-medium text-slate-900">{{ $log->user?->name ?? 'System' }}</div></td>
                            <td class="py-3 px-4">
                                @php $ab = match($log->action) { 'created'=>'bg-emerald-50 text-emerald-600','updated'=>'bg-blue-50 text-blue-600','deleted'=>'bg-red-50 text-red-500',default=>'bg-slate-100 text-slate-500' }; @endphp
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $ab }}">{{ strtoupper($log->action) }}</span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600 font-mono">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</td>
                            <td class="py-3 px-4 text-xs text-slate-400 font-mono">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-12 text-center text-slate-400"><div class="text-3xl mb-2">📋</div><div class="font-medium text-slate-600 mb-1">Belum Ada Log</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
