<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class UpdateNodeList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:update-node-list {--fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the cached node data';

    protected string $nodesJsonUrl = 'https://github.com/Sol-Sanctum/Zenon-PoCs/releases/download/znn_node_info/output_nodes.json';

    /**
     * @var ?Collection
     */
    protected ?Collection $nodes;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Update node list job queued');
        \App\Jobs\UpdateNodeList::dispatch();

        return self::SUCCESS;
    }
}
