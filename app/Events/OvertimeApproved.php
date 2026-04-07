<?php
namespace App\Events;
use App\Models\OvertimeRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OvertimeApproved { use Dispatchable, SerializesModels; public function __construct(public OvertimeRequest $overtimeRequest) {} }
