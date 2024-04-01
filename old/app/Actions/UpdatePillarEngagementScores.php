<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domains\Nom\Models\Pillar;
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
