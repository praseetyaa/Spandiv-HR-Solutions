<?php

namespace App\Models\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait HasAuditLog
{
    use LogsActivity;

    /**
     * Configure activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getLogName())
            ->setDescriptionForEvent(fn (string $eventName) => "{$eventName} " . class_basename($this));
    }

    /**
     * Get the log name for this model.
     */
    protected function getLogName(): string
    {
        return property_exists($this, 'logName')
            ? $this->logName
            : strtolower(class_basename($this));
    }

    /**
     * Get attributes that should be excluded from logging.
     */
    protected function getExcludedAttributes(): array
    {
        return property_exists($this, 'excludeFromLog')
            ? $this->excludeFromLog
            : ['password', 'remember_token'];
    }
}
