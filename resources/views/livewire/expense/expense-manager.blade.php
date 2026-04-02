<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Expense & Reimbursement</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola pengajuan expense dan reimbursement karyawan</p>
        </div>
        <button wire:click="openRequestForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Request
        </button>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 mb-6 p-1 bg-slate-100 rounded-xl w-fit">
        <button wire:click="$set('tab', 'requests')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'requests' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500' }}">📋 Requests</button>
        <button wire:click="$set('tab', 'categories')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'categories' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500' }}">🏷️ Kategori</button>
    </div>

    @if($tab === 'requests')
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <div class="relative flex-1 min-w-[200px] max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
            <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Status</option><option value="draft">Draft</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option><option value="paid">Paid</option></select>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1fr_380px] gap-6">
            <x-ui.card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Request</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Total</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                        </tr></thead>
                        <tbody>
                            @forelse($this->requests as $req)
                                <tr wire:click="selectRequest({{ $req->id }})" class="border-b border-slate-50 hover:bg-slate-50/50 cursor-pointer {{ $selectedRequestId === $req->id ? 'bg-brand-50/30' : '' }}">
                                    <td class="py-3 px-4"><div class="font-medium text-slate-900">{{ $req->title }}</div><div class="text-xs text-slate-400">{{ $req->expense_date->format('d M Y') }}</div></td>
                                    <td class="py-3 px-4 text-slate-600">{{ $req->employee->full_name }}</td>
                                    <td class="py-3 px-4 text-right font-bold text-slate-900">Rp {{ number_format($req->total_amount) }}</td>
                                    <td class="py-3 px-4"><x-ui.badge :type="$req->status_color" size="xs">{{ ucfirst($req->status) }}</x-ui.badge></td>
                                    <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1">
                                        @if($req->status === 'pending')<button wire:click.stop="approveRequest({{ $req->id }})" class="p-1.5 rounded-lg border-none bg-green-50 text-green-600 cursor-pointer hover:bg-green-100 transition-colors" title="Approve"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg></button><button wire:click.stop="rejectRequest({{ $req->id }})" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors" title="Reject"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>@endif
                                        @if($req->status === 'approved')<button wire:click.stop="markPaid({{ $req->id }})" class="p-1.5 rounded-lg border-none bg-blue-50 text-blue-600 cursor-pointer hover:bg-blue-100 transition-colors" title="Mark Paid">💳</button>@endif
                                        <button wire:click.stop="deleteRequest({{ $req->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                    </div></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-12 text-center text-slate-400">Belum ada request expense.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            {{-- Detail Panel --}}
            <div>
                @if($this->selectedRequest)
                    @php $sr = $this->selectedRequest; @endphp
                    <x-ui.card>
                        <h4 class="m-0 text-base font-bold text-slate-900 mb-1">{{ $sr->title }}</h4>
                        <p class="text-xs text-slate-400 mb-4">{{ $sr->employee->full_name }} · {{ $sr->expense_date->format('d M Y') }}</p>
                        <p class="text-sm text-slate-600 mb-4">{{ $sr->purpose }}</p>
                        <div class="border-t border-slate-100 pt-3">
                            @foreach($sr->items as $item)
                                <div class="flex items-center justify-between py-2 text-sm {{ !$loop->last ? 'border-b border-slate-50' : '' }}">
                                    <div><span class="text-slate-900">{{ $item->description }}</span><span class="text-xs text-slate-400 ml-2">{{ $item->category->name ?? '' }}</span></div>
                                    <span class="font-medium text-slate-900">Rp {{ number_format($item->amount) }}</span>
                                </div>
                            @endforeach
                            <div class="flex items-center justify-between pt-3 mt-2 border-t border-slate-200">
                                <span class="text-sm font-bold text-slate-900">Total</span>
                                <span class="text-lg font-extrabold text-slate-900">Rp {{ number_format($sr->total_amount) }}</span>
                            </div>
                        </div>
                    </x-ui.card>
                @else
                    <x-ui.card><div class="text-center py-12 text-slate-400"><div class="text-4xl mb-3">👈</div><p class="text-sm m-0">Pilih request untuk detail</p></div></x-ui.card>
                @endif
            </div>
        </div>
    @else
        <div class="flex justify-end mb-4"><button wire:click="openCatForm" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Tambah Kategori</button></div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->categories as $cat)
                <x-ui.card>
                    <div class="flex items-start justify-between mb-2">
                        <div><h4 class="m-0 text-sm font-bold text-slate-900">{{ $cat->name }}</h4><span class="text-xs font-mono text-slate-400">{{ $cat->code }}</span></div>
                        <div class="flex gap-1"><button wire:click="openCatForm({{ $cat->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button><button wire:click="deleteCat({{ $cat->id }})" wire:confirm="Hapus?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button></div>
                    </div>
                    <div class="flex items-center gap-3 mt-3 text-xs text-slate-400">
                        @if($cat->max_amount)<span>Max Rp {{ number_format($cat->max_amount) }}</span>@endif
                        @if($cat->requires_receipt)<span>📎 Bukti Wajib</span>@endif
                        @if($cat->requires_approval)<span>✅ Perlu Approval</span>@endif
                    </div>
                </x-ui.card>
            @empty
                <div class="col-span-full text-center py-8 text-slate-400 text-sm">Belum ada kategori expense.</div>
            @endforelse
        </div>
    @endif

    {{-- Category Form Modal --}}
    @if($showCatForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showCatForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingCatId ? 'Edit' : 'Tambah' }} Kategori</h3></div>
                <form wire:submit="saveCat" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4"><div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama <span class="text-danger">*</span></label><input type="text" wire:model="catName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div><div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kode <span class="text-danger">*</span></label><input type="text" wire:model="catCode" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="e.g. TRV"></div></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Max Amount</label><input type="number" wire:model="catMaxAmount" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" step="0.01"></div>
                    <div class="flex items-center gap-6"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="catRequiresReceipt" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700">Butuh Bukti</span></label><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="catRequiresApproval" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700">Butuh Approval</span></label></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showCatForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif

    {{-- Request Form Modal --}}
    @if($showRequestForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showRequestForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[640px] max-h-[85vh] overflow-y-auto">
                <div class="px-6 py-5 border-b border-slate-100 sticky top-0 bg-white z-10"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Buat Expense Request</h3></div>
                <form wire:submit="saveRequest" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="reqEmployeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select></div>
                    <div class="grid grid-cols-2 gap-4"><div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Judul <span class="text-danger">*</span></label><input type="text" wire:model="reqTitle" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div><div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal <span class="text-danger">*</span></label><input type="date" wire:model="reqDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tujuan <span class="text-danger">*</span></label><textarea wire:model="reqPurpose" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>

                    <div class="border-t border-slate-100 pt-4">
                        <div class="flex items-center justify-between mb-3"><h4 class="m-0 text-sm font-semibold text-slate-700">Item Expense</h4><button type="button" wire:click="addItem" class="text-xs font-medium text-brand hover:underline cursor-pointer bg-transparent border-none">+ Tambah Item</button></div>
                        @foreach($reqItems as $idx => $item)
                            <div class="flex items-start gap-2 mb-2 p-3 bg-slate-50 rounded-lg">
                                <div class="flex-1 grid grid-cols-3 gap-2">
                                    <select wire:model="reqItems.{{ $idx }}.category_id" class="form-input py-2 px-2.5 border border-slate-200 rounded-lg text-xs outline-none bg-white"><option value="">Kategori</option>@foreach($this->categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select>
                                    <input type="text" wire:model="reqItems.{{ $idx }}.description" class="form-input py-2 px-2.5 border border-slate-200 rounded-lg text-xs outline-none" placeholder="Deskripsi">
                                    <input type="number" wire:model="reqItems.{{ $idx }}.amount" class="form-input py-2 px-2.5 border border-slate-200 rounded-lg text-xs outline-none" step="0.01" placeholder="Jumlah">
                                </div>
                                <button type="button" wire:click="removeItem({{ $idx }})" class="p-1.5 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500 shrink-0 mt-0.5"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showRequestForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Submit</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
