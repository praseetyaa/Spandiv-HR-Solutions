<?php
namespace App\Console\Commands;

use App\Jobs\CalculateLeaveBalanceJob;
use App\Models\Tenant;
use Illuminate\Console\Command;

class ResetLeaveBalanceCommand extends Command
{
    protected $signature = 'hr:reset-leave-balance {--year= : Target year, defaults to current year}';
    protected $description = 'Reset annual leave balances for all tenants with carry-over calculation';

    public function handle(): int
    {
        $year = (int) ($this->option('year') ?: now()->year);

        $tenants = Tenant::where('status', 'active')->get();

        foreach ($tenants as $tenant) {
            CalculateLeaveBalanceJob::dispatch($tenant->id, $year);
            $this->info("Dispatched leave balance reset for Tenant #{$tenant->id} ({$tenant->name})");
        }

        $this->info("Leave balance reset dispatched for {$tenants->count()} tenants.");
        return self::SUCCESS;
    }
}
