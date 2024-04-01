<?php

declare(strict_types=1);

namespace App\Jobs\Sync;

use App\Actions\Nom\Accelerator\SyncProjectStatus;
use App\Domains\Nom\Enums\AcceleratorProjectStatusEnum;
use App\Domains\Nom\Models\AcceleratorProject;
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
            AcceleratorProjectStatusEnum::NEW->value,
            AcceleratorProjectStatusEnum::ACCEPTED->value,
        ])->get();

        $projects->each(function ($project) {
            (new SyncProjectStatus($project))->execute();
        });
    }
}
