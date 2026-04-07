<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes / Scheduled Commands
|--------------------------------------------------------------------------
*/

// Cek kontrak yang akan habis (30, 14, 7 hari sebelumnya)
Schedule::command('hr:check-contract-expiry')->dailyAt('08:00');

// Bersihkan sesi tes psikologi yang expired
Schedule::command('hr:clean-expired-test-sessions')->dailyAt('02:00');

// Proses notifikasi pending (in-app, email, whatsapp)
Schedule::command('hr:process-pending-notifications')->everyMinute();

// Reset saldo cuti awal tahun
Schedule::command('hr:reset-leave-balance')->yearlyOn(1, 1, '00:30');

// Kirim reminder absensi pagi
Schedule::command('hr:send-attendance-reminder')->dailyAt('07:30');

// Kirim ringkasan HR mingguan ke company owner
Schedule::command('hr:weekly-summary')->weeklyOn(1, '09:00');
