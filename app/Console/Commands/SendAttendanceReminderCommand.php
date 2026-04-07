<?php
namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Attendance;
use App\Services\NotificationDispatchService;
use Illuminate\Console\Command;

class SendAttendanceReminderCommand extends Command
{
    protected $signature = 'hr:attendance-reminder';
    protected $description = 'Send attendance reminder to employees who have not clocked in';

    public function handle(NotificationDispatchService $notif): int
    {
        $today = now()->toDateString();

        $employees = Employee::where('status', 'active')
            ->whereDoesntHave('attendances', fn ($q) => $q->where('date', $today))
            ->with('user')
            ->get();

        foreach ($employees as $emp) {
            if (!$emp->user) continue;

            $notif->send('attendance_reminder', $emp->user, [
                'title'   => 'Reminder Absensi',
                'message' => 'Anda belum melakukan clock-in hari ini. Silakan lakukan absensi segera.',
            ]);
        }

        $this->info("Sent attendance reminders to {$employees->count()} employees.");
        return self::SUCCESS;
    }
}
