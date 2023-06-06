<?php

namespace App\Jobs\Sync;

use App\Actions\Nom\SyncProjectStatus;
use App\Models\Nom\AcceleratorProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProjectStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $projects = AcceleratorProject::whereIn('status', [
            AcceleratorProject::STATUS_NEW,
            AcceleratorProject::STATUS_ACCEPTED,
        ])->get();

        $projects->each(function ($project) {
            (new SyncProjectStatus($project))->execute();
        });
    }
}
