<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services\Sdk;

use App\Domains\Nom\DataTransferObjects\PillarDTO;
use App\Domains\Nom\Exceptions\ZenonRpcException;
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
