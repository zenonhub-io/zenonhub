<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services\Sdk;

trait Accelerator
{
    public function AcceleratorGetAll(int $page = 0, int $perPage = 1000): array
    {
        return $this->sdk->accelerator->getAll($page, $perPage);
    }

    public function AcceleratorGetProjectById(string $id): array
    {
        return $this->sdk->accelerator->getProjectById($id);
    }

    public function AcceleratorGetPhaseById(string $id): array
    {
        return $this->sdk->accelerator->getPhaseById($id);
    }

    public function AcceleratorGetPillarVotes(string $pillarName, array $projectHashes = []): array
    {
        return $this->sdk->accelerator->getPillarVotes($pillarName, $projectHashes);
    }

    public function AcceleratorGetVoteBreakdown(string $hash): array
    {
        return $this->sdk->accelerator->getVoteBreakdown($hash);
    }
}
