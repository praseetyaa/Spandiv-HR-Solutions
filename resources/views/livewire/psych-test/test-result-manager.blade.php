<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Hasil Tes Psikologi</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Review hasil, skor dimensi, dan rekomendasi kandidat</p>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php $st = $this->stats; @endphp
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-slate-700">{{ $st['total'] }}</div><div class="text-xs font-medium text-slate-400 mt-1">Total Hasil</div></div></x-ui.card>
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-emerald-600">{{ $st['highly_recommended'] }}</div><div class="text-xs font-medium text-emerald-500 mt-1">⭐ Highly Rec.</div></div></x-ui.card>
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-blue-600">{{ $st['recommended'] }}</div><div class="text-xs font-medium text-blue-500 mt-1">✅ Recommended</div></div></x-ui.card>
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-red-600">{{ $st['not_recommended'] }}</div><div class="text-xs font-medium text-red-500 mt-1">❌ Not Rec.</div></div></x-ui.card>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kandidat..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="recommendationFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Rekomendasi</option><option value="highly_recommended">⭐ Highly Recommended</option><option value="recommended">✅ Recommended</option><option value="not_recommended">❌ Not Recommended</option><option value="pending">⏳ Pending</option></select>
        <select wire:model.live="testFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Tes</option>@foreach($this->tests as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-3">
            <x-ui.card :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Kandidat</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Tes</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Skor</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Grade</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Rekomendasi</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                        </tr></thead>
                        <tbody>
                            @forelse($this->results as $r)
                                @php $recBadge = match($r->overall_recommendation) { 'highly_recommended'=>['⭐ HR','bg-emerald-50 text-emerald-600'],'recommended'=>['✅ Rec','bg-blue-50 text-blue-600'],'not_recommended'=>['❌ NR','bg-red-50 text-red-500'],default=>['⏳','bg-amber-50 text-amber-600'] }; @endphp
                                <tr wire:click="selectResult({{ $r->id }})" class="border-b border-slate-50 hover:bg-slate-50/50 cursor-pointer {{ $selectedResultId === $r->id ? 'bg-brand-50' : '' }}">
                                    <td class="py-3 px-4"><div class="font-medium text-slate-900">{{ $r->assignment?->candidate?->name ?? '-' }}</div></td>
                                    <td class="py-3 px-4 text-slate-500">{{ $r->assignment?->test?->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center"><span class="text-sm font-bold text-brand">{{ number_format($r->scaled_score, 1) }}</span></td>
                                    <td class="py-3 px-4 text-center"><span class="text-xs font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">{{ $r->grade }}</span></td>
                                    <td class="py-3 px-4 text-center"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $recBadge[1] }}">{{ $recBadge[0] }}</span></td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button wire:click.stop="openReviewForm({{ $r->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                            @if(!$r->is_published)<button wire:click.stop="publishResult({{ $r->id }})" class="p-1.5 rounded-lg border-none bg-emerald-50 text-emerald-500 cursor-pointer hover:bg-emerald-100 transition-colors text-[11px] font-semibold px-2">Publish</button>@endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-12 text-center text-slate-400"><div class="text-3xl mb-2">📊</div><div class="font-medium text-slate-600 mb-1">Belum Ada Hasil</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>

        <div class="lg:col-span-2">
            <x-ui.card>
                @if($this->selectedResult)
                    @php $sr = $this->selectedResult; @endphp
                    <h3 class="m-0 text-lg font-bold text-slate-900 mb-1">{{ $sr->assignment?->candidate?->name }}</h3>
                    <p class="text-xs text-slate-400 mb-4">{{ $sr->assignment?->test?->name }}</p>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="text-center p-2 bg-slate-50 rounded-lg"><div class="text-sm font-bold text-slate-700">{{ number_format($sr->raw_score,1) }}</div><div class="text-[10px] text-slate-400">Raw</div></div>
                        <div class="text-center p-2 bg-brand-50 rounded-lg"><div class="text-sm font-bold text-brand">{{ number_format($sr->scaled_score,1) }}</div><div class="text-[10px] text-slate-400">Scaled</div></div>
                        <div class="text-center p-2 bg-purple-50 rounded-lg"><div class="text-sm font-bold text-purple-600">{{ $sr->grade }}</div><div class="text-[10px] text-slate-400">Grade</div></div>
                    </div>
                    @if($sr->dimension_scores && is_array($sr->dimension_scores))
                        <div class="text-xs font-semibold text-slate-500 uppercase mb-2">Dimensi</div>
                        <div class="space-y-2 mb-4">
                            @foreach($sr->dimension_scores as $dim => $score)
                                <div>
                                    <div class="flex justify-between text-[11px] mb-0.5"><span class="text-slate-600 font-medium">{{ $dim }}</span><span class="font-bold text-slate-700">{{ $score }}</span></div>
                                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-brand rounded-full" style="width: {{ min($score, 100) }}%"></div></div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if($sr->reviewer_notes)<div class="text-xs font-semibold text-slate-500 uppercase mb-1">Catatan Reviewer</div><p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg mb-4">{{ $sr->reviewer_notes }}</p>@endif
                    <x-ui.badge :type="$sr->recommendation_color" size="sm">{{ $sr->recommendation_label }}</x-ui.badge>
                @else
                    <div class="text-center py-12"><div class="text-3xl mb-2">👆</div><div class="text-sm text-slate-400">Pilih hasil untuk detail</div></div>
                @endif
            </x-ui.card>
        </div>
    </div>

    @if($showReviewForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showReviewForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Review Hasil</h3></div>
                <form wire:submit="saveReview" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Rekomendasi</label><select wire:model="recommendation" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="pending">⏳ Pending</option><option value="highly_recommended">⭐ Highly Recommended</option><option value="recommended">✅ Recommended</option><option value="not_recommended">❌ Not Recommended</option></select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Catatan</label><textarea wire:model="reviewerNotes" rows="4" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showReviewForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
