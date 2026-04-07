<?php
namespace App\Jobs;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $tenantId, public array $records) {}

    public function handle(): void
    {
        foreach ($this->records as $record) {
            $employee = Employee::where('tenant_id', $this->tenantId)
                ->where('employee_number', $record['employee_number'])
                ->first();

            if (!$employee) continue;

            Attendance::updateOrCreate(
                ['employee_id' => $employee->id, 'date' => $record['date']],
                [
                    'tenant_id'       => $this->tenantId,
                    'clock_in'        => $record['clock_in'] ?? null,
                    'clock_out'       => $record['clock_out'] ?? null,
                    'clock_in_method' => 'fingerprint',
                    'status'          => $record['status'] ?? 'present',
                ]
            );
        }
    }
}
