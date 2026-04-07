<?php
namespace App\Console\Commands;

use App\Jobs\SendScheduledNotificationJob;
use Illuminate\Console\Command;

class ProcessPendingNotificationsCommand extends Command
{
    protected $signature = 'hr:process-notifications';
    protected $description = 'Process all pending scheduled notifications';

    public function handle(): int
    {
        SendScheduledNotificationJob::dispatch();
        $this->info('Dispatched pending notifications job.');
        return self::SUCCESS;
    }
}
