<?php

namespace App\Console\Commands;

use Notification;
use Illuminate\Console\Command;

class ProjectVotingReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:project-voting-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the voting reminder emails to pillar owners';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \App\Jobs\Accelerator\SendVotingReminders::dispatch();
        return self::SUCCESS;
    }
}
