<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Nom\Models\Momentum;
use Illuminate\Console\Command;
use Log;

class CheckIndexer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:check-indexer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks that the indexer has produced a momentum in the last 15 minutes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $latestMomentum = Momentum::latest()->first();
        if ($latestMomentum->created_at < now()->subMinutes(15)) {
            $this->error('Indexer NOT running');

            Log::critical('Indexer has stopped running, last momentum:', [
                'height' => $latestMomentum->height,
                'date' => $latestMomentum->created_at->format('Y-m-d H:i:s'),
            ]);

            return self::FAILURE;
        }

        $this->info('Indexer running');

        return self::SUCCESS;
    }
}
