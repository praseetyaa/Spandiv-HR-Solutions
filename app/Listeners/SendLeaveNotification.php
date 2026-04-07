<?php
namespace App\Listeners;

use App\Events\LeaveApproved;
use App\Events\LeaveRejected;
use App\Events\LeaveRequested;
use App\Services\NotificationDispatchService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLeaveNotification implements ShouldQueue
{
    public function __construct(private NotificationDispatchService $notif) {}

    public function handleRequested(LeaveRequested $event): void
    {
        $lr = $event->leaveRequest->load(['employee.manager.user', 'leaveType']);
        $manager = $lr->employee->manager;
        if (!$manager?->user) return;

        $this->notif->send('leave_requested', $manager->user, [
            'employee_name' => $lr->employee->full_name,
            'leave_type'    => $lr->leaveType->name,
            'start_date'    => $lr->start_date->format('d/m/Y'),
            'end_date'      => $lr->end_date->format('d/m/Y'),
            'total_days'    => $lr->total_days,
            'action_url'    => route('attendance.leave'),
        ]);
    }

    public function handleApproved(LeaveApproved $event): void
    {
        $lr = $event->leaveRequest->load(['employee.user', 'leaveType']);
        if (!$lr->employee->user) return;

        $this->notif->send('leave_approved', $lr->employee->user, [
            'leave_type' => $lr->leaveType->name,
            'start_date' => $lr->start_date->format('d/m/Y'),
            'end_date'   => $lr->end_date->format('d/m/Y'),
        ]);
    }

    public function handleRejected(LeaveRejected $event): void
    {
        $lr = $event->leaveRequest->load(['employee.user', 'leaveType']);
        if (!$lr->employee->user) return;

        $this->notif->send('leave_rejected', $lr->employee->user, [
            'leave_type' => $lr->leaveType->name,
            'reason'     => $lr->rejection_reason,
        ]);
    }
}
