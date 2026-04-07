<?php
namespace App\Events;
use App\Models\EmployeeContract;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractExpiring { use Dispatchable, SerializesModels; public function __construct(public EmployeeContract $contract, public int $daysRemaining) {} }
