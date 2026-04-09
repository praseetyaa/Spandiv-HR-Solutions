{{-- Navbar Component --}}
<header class="hr-navbar sticky top-0 z-30 border-b border-black/[0.06] px-4 md:px-8 h-16 flex items-center justify-between">
    {{-- Left side --}}
    <div class="flex items-center gap-4">
        {{-- Mobile hamburger toggle --}}
        <button class="hidden lg:!hidden max-lg:flex items-center justify-center p-2 rounded-lg border-none bg-transparent cursor-pointer text-slate-500" x-on:click="$store.sidebar.toggle()">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        <h1 class="text-xl font-bold text-slate-900 m-0">
            {{ $pageTitle ?? 'Dashboard' }}
        </h1>
    </div>

    {{-- Right side --}}
    <div class="flex items-center gap-3">
        {{-- Search --}}
        <div class="relative" x-data="navbarSearch()">
            <button class="p-2.5 rounded-[10px] border-none bg-slate-100 cursor-pointer text-slate-500 transition-all duration-200 flex items-center justify-center hover:bg-slate-200" x-on:click="toggle()" id="navbar-search-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </button>

            <div
                x-show="open"
                x-on:click.outside="close()"
                x-on:keydown.escape.window="close()"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                class="absolute right-0 top-[calc(100%+8px)] bg-white rounded-xl shadow-[0_16px_48px_rgba(0,0,0,0.12)] border border-black/[0.06] z-[100] w-80 overflow-hidden"
                id="navbar-search-dropdown"
            >
                <div class="p-3 border-b border-slate-100">
                    <input
                        type="text"
                        x-model="query"
                        x-on:input="filter()"
                        x-on:keydown.arrow-down.prevent="moveDown()"
                        x-on:keydown.arrow-up.prevent="moveUp()"
                        x-on:keydown.enter.prevent="goToSelected()"
                        x-ref="searchInput"
                        placeholder="Cari menu..."
                        class="w-full py-2.5 px-3.5 border border-slate-200 rounded-lg text-sm outline-none font-[Inter,sans-serif] box-border focus:border-brand focus:ring-2 focus:ring-brand/10"
                        id="navbar-search-input"
                    >
                </div>
                <div class="max-h-64 overflow-y-auto p-1.5" x-show="filteredItems.length > 0">
                    <template x-for="(item, index) in filteredItems" :key="item.route">
                        <a
                            :href="item.url"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg no-underline text-slate-700 text-sm transition-colors duration-150"
                            :class="selectedIndex === index ? 'bg-brand/10 text-brand' : 'hover:bg-slate-100'"
                            x-on:mouseenter="selectedIndex = index"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-slate-400">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                            <span x-text="item.label"></span>
                            <span class="ml-auto text-[11px] text-slate-400" x-text="item.group"></span>
                        </a>
                    </template>
                </div>
                <div class="p-4 text-center text-slate-400 text-sm" x-show="query.length > 0 && filteredItems.length === 0">
                    Tidak ditemukan
                </div>
                <div class="p-3 border-t border-slate-100 text-[11px] text-slate-400 text-center" x-show="filteredItems.length > 0">
                    <kbd class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 font-mono text-[10px]">↑↓</kbd> navigasi
                    <kbd class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 font-mono text-[10px] ml-2">Enter</kbd> buka
                    <kbd class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 font-mono text-[10px] ml-2">Esc</kbd> tutup
                </div>
            </div>
        </div>

        {{-- Notifications --}}
        <div class="relative" x-data="{ notifOpen: false }">
            <button class="p-2.5 rounded-[10px] border-none bg-slate-100 cursor-pointer text-slate-500 transition-all duration-200 relative flex items-center justify-center hover:bg-slate-200" x-on:click="notifOpen = !notifOpen" id="navbar-notif-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-danger rounded-full border-2 border-slate-100"></span>
            </button>

            <div
                x-show="notifOpen"
                x-on:click.outside="notifOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="absolute right-0 top-[calc(100%+8px)] bg-white rounded-xl shadow-[0_16px_48px_rgba(0,0,0,0.12)] border border-black/[0.06] z-[100] w-[360px] max-h-[400px] overflow-y-auto"
            >
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <span class="font-semibold text-[15px] text-slate-900">Notifikasi</span>
                    <span class="text-xs text-brand cursor-pointer font-medium">Tandai semua dibaca</span>
                </div>
                <div class="p-5 text-center text-slate-400 text-sm">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-3 block">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                    </svg>
                    <p class="m-0">Belum ada notifikasi</p>
                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div class="w-px h-8 bg-slate-200 max-sm:hidden"></div>

        {{-- Profile --}}
        <div class="relative" x-data="{ profileOpen: false }">
            <button class="flex items-center gap-2.5 py-1.5 pl-1.5 pr-2.5 rounded-[10px] border-none bg-transparent cursor-pointer transition-all duration-200 hover:bg-slate-100" x-on:click="profileOpen = !profileOpen" id="navbar-profile-btn">
                <div class="profile-avatar w-9 h-9 rounded-[10px] flex items-center justify-center text-white font-semibold text-sm shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="text-left max-sm:hidden">
                    <div class="text-[13px] font-semibold text-slate-900 leading-tight">
                        {{ auth()->user()->name ?? 'User' }}
                    </div>
                    <div class="text-[11px] text-slate-400 leading-tight">
                        {{ auth()->user()->isPlatformUser() ? 'Platform Admin' : 'Tenant User' }}
                    </div>
                </div>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>

            <div
                x-show="profileOpen"
                x-on:click.outside="profileOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                class="absolute right-0 top-[calc(100%+8px)] bg-white rounded-xl shadow-[0_16px_48px_rgba(0,0,0,0.12)] border border-black/[0.06] z-[100] w-[220px] p-1.5"
                id="navbar-profile-dropdown"
            >
                <a href="{{ route('settings.profile') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-lg no-underline text-slate-700 text-sm transition-colors duration-200 hover:bg-slate-100" id="navbar-profile-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    Profil Saya
                </a>
                <a href="{{ route('settings.general') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-lg no-underline text-slate-700 text-sm transition-colors duration-200 hover:bg-slate-100" id="navbar-settings-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Pengaturan
                </a>
                <div class="h-px bg-slate-100 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-lg text-danger text-sm w-full text-left border-none bg-transparent cursor-pointer font-[inherit] transition-colors duration-200 hover:bg-red-50" id="navbar-logout-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
function navbarSearch() {
    return {
        open: false,
        query: '',
        selectedIndex: 0,
        items: [
            { label: 'Dashboard', route: 'dashboard', url: '{{ route("dashboard") }}', group: '' },
            { label: 'Data Karyawan', route: 'employees.index', url: '{{ route("employees.index") }}', group: 'Karyawan' },
            { label: 'Departemen', route: 'employees.departments', url: '{{ route("employees.departments") }}', group: 'Karyawan' },
            { label: 'Jabatan', route: 'employees.positions', url: '{{ route("employees.positions") }}', group: 'Karyawan' },
            { label: 'Absensi', route: 'attendance.index', url: '{{ route("attendance.index") }}', group: 'Kehadiran' },
            { label: 'Cuti & Izin', route: 'attendance.leave', url: '{{ route("attendance.leave") }}', group: 'Kehadiran' },
            { label: 'Lembur', route: 'attendance.overtime', url: '{{ route("attendance.overtime") }}', group: 'Kehadiran' },
            { label: 'Penggajian', route: 'payroll.index', url: '{{ route("payroll.index") }}', group: 'Payroll' },
            { label: 'Komponen Gaji', route: 'payroll.components', url: '{{ route("payroll.components") }}', group: 'Payroll' },
            { label: 'Bonus', route: 'payroll.bonus', url: '{{ route("payroll.bonus") }}', group: 'Payroll' },
            { label: 'Lowongan', route: 'recruitment.postings', url: '{{ route("recruitment.postings") }}', group: 'Rekrutmen' },
            { label: 'Kandidat', route: 'recruitment.candidates', url: '{{ route("recruitment.candidates") }}', group: 'Rekrutmen' },
            { label: 'Onboarding', route: 'recruitment.onboarding', url: '{{ route("recruitment.onboarding") }}', group: 'Rekrutmen' },
            { label: 'Bank Tes', route: 'psych-test.tests', url: '{{ route("psych-test.tests") }}', group: 'Tes Psikologi' },
            { label: 'Penugasan Tes', route: 'psych-test.assignments', url: '{{ route("psych-test.assignments") }}', group: 'Tes Psikologi' },
            { label: 'Hasil Tes', route: 'psych-test.results', url: '{{ route("psych-test.results") }}', group: 'Tes Psikologi' },
            { label: 'Performa', route: 'performance.cycles', url: '{{ route("performance.cycles") }}', group: 'Pengembangan' },
            { label: 'Goal / KPI', route: 'performance.goals', url: '{{ route("performance.goals") }}', group: 'Pengembangan' },
            { label: 'Talent 9-Box', route: 'talent.nine-box', url: '{{ route("talent.nine-box") }}', group: 'Pengembangan' },
            { label: 'Survei', route: 'engagement.surveys', url: '{{ route("engagement.surveys") }}', group: 'Engagement' },
            { label: 'Rekognisi', route: 'engagement.recognition', url: '{{ route("engagement.recognition") }}', group: 'Engagement' },
            { label: 'Pengumuman', route: 'engagement.announcements', url: '{{ route("engagement.announcements") }}', group: 'Engagement' },
            { label: 'Pengaturan Umum', route: 'settings.general', url: '{{ route("settings.general") }}', group: 'Pengaturan' },
            { label: 'Notifikasi', route: 'settings.notifications', url: '{{ route("settings.notifications") }}', group: 'Pengaturan' },
            { label: 'Audit Log', route: 'settings.audit-log', url: '{{ route("settings.audit-log") }}', group: 'Pengaturan' },
            { label: 'API Token', route: 'settings.api', url: '{{ route("settings.api") }}', group: 'Pengaturan' },
            { label: 'Profil Saya', route: 'settings.profile', url: '{{ route("settings.profile") }}', group: 'Pengaturan' },
        ],
        filteredItems: [],

        init() {
            this.filteredItems = this.items;
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.$refs.searchInput.focus());
            }
        },

        close() {
            this.open = false;
            this.query = '';
            this.filteredItems = this.items;
            this.selectedIndex = 0;
        },

        filter() {
            const q = this.query.toLowerCase().trim();
            if (q === '') {
                this.filteredItems = this.items;
            } else {
                this.filteredItems = this.items.filter(item =>
                    item.label.toLowerCase().includes(q) ||
                    item.group.toLowerCase().includes(q)
                );
            }
            this.selectedIndex = 0;
        },

        moveDown() {
            if (this.selectedIndex < this.filteredItems.length - 1) {
                this.selectedIndex++;
            }
        },

        moveUp() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
            }
        },

        goToSelected() {
            if (this.filteredItems.length > 0 && this.filteredItems[this.selectedIndex]) {
                window.location.href = this.filteredItems[this.selectedIndex].url;
            }
        }
    };
}
</script>
