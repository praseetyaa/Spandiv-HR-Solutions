<div>
    <form wire:submit="save" class="flex flex-col gap-5">
        {{-- Employee Select --}}
        <div>
            <label for="employeeId" class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label>
            <select
                wire:model="employeeId"
                id="employeeId"
                class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm text-slate-900 outline-none bg-white"
                {{ $profileId ? 'disabled' : '' }}
            >
                <option value="">Pilih Karyawan...</option>
                @foreach($this->employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->full_name }} — {{ $emp->jobPosition?->name ?? '-' }}</option>
                @endforeach
            </select>
            @error('employeeId') <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            {{-- Potential Level --}}
            <div>
                <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Potensi <span class="text-danger">*</span></label>
                <select wire:model="potential_level" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="very_high">Very High</option>
                </select>
            </div>

            {{-- Performance Level --}}
            <div>
                <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Performa <span class="text-danger">*</span></label>
                <select wire:model="performance_level" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">
                    <option value="below">Below Expectation</option>
                    <option value="meets">Meets Expectation</option>
                    <option value="exceeds">Exceeds Expectation</option>
                    <option value="outstanding">Outstanding</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            {{-- Flight Risk --}}
            <div>
                <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Flight Risk</label>
                <select wire:model="flight_risk" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            {{-- Successor Ready --}}
            <div class="flex items-end pb-1">
                <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 font-medium">
                    <input type="checkbox" wire:model="is_successor_ready" class="accent-brand w-4 h-4 cursor-pointer">
                    Siap menjadi successor
                </label>
            </div>
        </div>

        {{-- Strengths --}}
        <div>
            <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kekuatan</label>
            <textarea
                wire:model="strengths"
                rows="3"
                class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm text-slate-900 outline-none resize-y font-[Inter,sans-serif]"
                placeholder="Apa kekuatan utama karyawan ini?"
            ></textarea>
        </div>

        {{-- Development Notes --}}
        <div>
            <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Catatan Pengembangan</label>
            <textarea
                wire:model="development_notes"
                rows="3"
                class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm text-slate-900 outline-none resize-y font-[Inter,sans-serif]"
                placeholder="Area yang perlu dikembangkan..."
            ></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-2.5 pt-2">
            <button type="button" wire:click="$dispatch('close-modal')" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50 transition-colors">
                Batal
            </button>
            <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                {{ $profileId ? 'Simpan Perubahan' : 'Tambah Profil' }}
            </button>
        </div>
    </form>
</div>
