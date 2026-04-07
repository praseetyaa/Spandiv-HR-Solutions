<?php
namespace App\Console\Commands;

use App\Events\ContractExpiring;
use App\Models\EmployeeContract;
use Illuminate\Console\Command;

class CheckContractExpiryCommand extends Command
{
    protected $signature = 'hr:check-contract-expiry';
    protected $description = 'Check for expiring employee contracts and send notifications';

    public function handle(): int
    {
        $warningDays = config('hr.contract_warning_days', [30, 14, 7]);

        foreach ($warningDays as $days) {
            $contracts = EmployeeContract::whereDate('end_date', now()->addDays($days)->toDateString())
                ->where('status', 'active')
                ->with('employee')
                ->get();

            foreach ($contracts as $contract) {
                ContractExpiring::dispatch($contract, $days);
                $this->info("Contract #{$contract->id} ({$contract->employee->full_name}) expires in {$days} days");
            }
        }

        $this->info('Contract expiry check completed.');
        return self::SUCCESS;
    }
}
