<?php
namespace App\Observers;

use App\Events\LeaveApproved;
use App\Events\LeaveRejected;
use App\Events\LeaveRequested;
use App\Models\LeaveRequest;
use App\Services\LeaveBalanceService;

class LeaveRequestObserver
{
    public function __construct(private LeaveBalanceService $leaveBalanceService) {}

    public function created(LeaveRequest $request): void
    {
        LeaveRequested::dispatch($request);
    }

    public function updated(LeaveRequest $request): void
    {
        if (!$request->wasChanged('status')) return;

        match ($request->status) {
            'approved' => $this->handleApproved($request),
            'rejected' => LeaveRejected::dispatch($request),
            'cancelled' => $this->handleCancelled($request),
            default => null,
        };
    }

    private function handleApproved(LeaveRequest $request): void
    {
        $this->leaveBalanceService->deductBalance($request);
        LeaveApproved::dispatch($request);
    }

    private function handleCancelled(LeaveRequest $request): void
    {
        if ($request->getOriginal('status') === 'approved') {
            $this->leaveBalanceService->restoreBalance($request);
        }
    }
}
