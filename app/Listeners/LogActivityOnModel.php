<?php
namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogActivityOnModel implements ShouldQueue
{
    public function handle(object $event): void
    {
        $model = $event->{$this->getModelProperty($event)};
        if (!$model) return;

        AuditLog::create([
            'tenant_id'  => $model->tenant_id ?? auth()->user()?->tenant_id,
            'user_id'    => auth()->id(),
            'action'     => $this->getAction($event),
            'model_type' => get_class($model),
            'model_id'   => $model->id,
            'new_values' => $model->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    private function getModelProperty(object $event): string
    {
        $props = get_object_vars($event);
        return array_key_first($props) ?? 'model';
    }

    private function getAction(object $event): string
    {
        $class = class_basename($event);
        return match (true) {
            str_contains($class, 'Created')   => 'created',
            str_contains($class, 'Approved')  => 'updated',
            str_contains($class, 'Rejected')  => 'updated',
            str_contains($class, 'Published') => 'updated',
            str_contains($class, 'Completed') => 'updated',
            str_contains($class, 'Assigned')  => 'created',
            default => strtolower($class),
        };
    }
}
