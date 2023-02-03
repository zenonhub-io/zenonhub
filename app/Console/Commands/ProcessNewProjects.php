<?php

namespace App\Console\Commands;

use App\Models\Nom\AcceleratorProject;
use Illuminate\Console\Command;

class ProcessNewProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:process-new-projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks new projects where voting has ended and update the status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $projects = AcceleratorProject::where('status', AcceleratorProject::STATUS_NEW)
            ->where('created_at', '<=', now()->subDays(14))
            ->get();

        $projects->each(function ($project) {
            \App\Jobs\Sync\ProjectStatus::dispatch($project);
        });

        return self::SUCCESS;
    }
}
