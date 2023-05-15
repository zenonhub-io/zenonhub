<?php

namespace App\Actions;

use App\Models\Nom\Pillar;
use Spatie\QueueableAction\QueueableAction;

class UpdatePillarEngagementScores
{
    use QueueableAction;

    public function __construct()
    {
    }

    public function execute(): void
    {
        $pillars = Pillar::isActive()->get();
        $pillars->each(function ($pillar) {
            $pillar->updateAzEngagementScores();
        });
    }
}
