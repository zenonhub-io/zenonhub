<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ExportCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUserOfCompletedExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    public string $export;

    public function __construct(User $user, string $export)
    {
        $this->user = $user;
        $this->export = $export;
    }

    public function handle(): void
    {
        $this->user->notify(new ExportCompleted($this->export));
    }
}
