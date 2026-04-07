<?php
namespace App\Events;
use App\Models\PayrollPeriod;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayrollPublished { use Dispatchable, SerializesModels; public function __construct(public PayrollPeriod $period) {} }
