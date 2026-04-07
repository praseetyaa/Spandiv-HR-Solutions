<?php
namespace App\Jobs;

use App\Models\Tenant;
use App\Services\LeaveBalanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateLeaveBalanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $tenantId, public int $year) {}

    public function handle(LeaveBalanceService $service): void
    {
        $service->resetAnnualBalances($this->tenantId, $this->year);
    }
}
