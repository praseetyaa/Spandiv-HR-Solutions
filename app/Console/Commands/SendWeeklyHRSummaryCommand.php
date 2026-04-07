<?php
namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Services\NotificationDispatchService;
use Illuminate\Console\Command;

class SendWeeklyHRSummaryCommand extends Command
{
    protected $signature = 'hr:weekly-summary';
    protected $description = 'Send weekly HR summary to company owners and HR admins';

    public function handle(NotificationDispatchService $notif): int
    {
        $tenants = Tenant::where('status', 'active')->get();

        foreach ($tenants as $tenant) {
            $startOfWeek = now()->startOfWeek();
            $endOfWeek   = now()->endOfWeek();

            $stats = [
                'total_employees' => Employee::where('tenant_id', $tenant->id)->where('status', 'active')->count(),
                'new_hires'       => Employee::where('tenant_id', $tenant->id)->whereBetween('join_date', [$startOfWeek, $endOfWeek])->count(),
                'pending_leaves'  => LeaveRequest::where('tenant_id', $tenant->id)->where('status', 'pending')->count(),
                'avg_attendance'  => Attendance::where('tenant_id', $tenant->id)->whereBetween('date', [$startOfWeek, $endOfWeek])->count(),
            ];

            $admins = \App\Models\User::where('tenant_id', $tenant->id)
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['company_owner', 'hr_admin']))
                ->get();

            foreach ($admins as $admin) {
                $notif->send('weekly_hr_summary', $admin, array_merge($stats, [
                    'title' => 'Ringkasan HR Mingguan',
                ]));
            }
        }

        $this->info("Weekly HR summary sent.");
        return self::SUCCESS;
    }
}
