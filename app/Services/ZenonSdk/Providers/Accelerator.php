<?php

declare(strict_types=1);

namespace App\Services\ZenonSdk\Providers;

use App\DataTransferObjects\Nom\AcceleratorPhaseDTO;
use App\DataTransferObjects\Nom\AcceleratorProjectDTO;
use App\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

trait Accelerator
{
    public function getProjectById(string $id): AcceleratorProjectDTO
    {
        try {
            $data = $this->sdk->accelerator->getProjectById($id)['data'];

            return AcceleratorProjectDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getProjectById');
        }
    }

    public function getPhaseById(string $id): AcceleratorPhaseDTO
    {
        try {
            $data = $this->sdk->accelerator->getPhaseById($id)['data'];

            return AcceleratorPhaseDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getProjectById');
        }
    }
}
