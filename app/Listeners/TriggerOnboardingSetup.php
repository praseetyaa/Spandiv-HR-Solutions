<?php
namespace App\Listeners;

use App\Events\OnboardingTaskCompleted;
use App\Services\OnboardingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class TriggerOnboardingSetup implements ShouldQueue
{
    public function __construct(private OnboardingService $service) {}

    public function handle(OnboardingTaskCompleted $event): void
    {
        $this->service->recalculateProgress($event->task->onboarding);
    }
}
