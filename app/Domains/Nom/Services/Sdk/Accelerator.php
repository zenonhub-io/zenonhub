<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services\Sdk;

use App\Domains\Nom\DataTransferObjects\AcceleratorPhaseDTO;
use App\Domains\Nom\DataTransferObjects\AcceleratorProjectDTO;
use App\Domains\Nom\Exceptions\ZenonRpcException;
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
