<?php

declare(strict_types=1);

namespace App\Services\ZenonSdk;

use App\DataTransferObjects\Nom\PillarDTO;
use App\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

trait Pillar
{
    public function getPillarByName(string $name): PillarDTO
    {
        try {
            $data = $this->sdk->pillar->getByName($name)['data'];

            return PillarDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getProjectById');
        }
    }

    public function getPillarByOwner(string $address): PillarDTO
    {
        try {
            $data = $this->sdk->pillar->getByOwner($address)['data'];

            return PillarDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getProjectById');
        }
    }
}
