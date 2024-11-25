<?php

declare(strict_types=1);

namespace App\Services\ZenonSdk\Providers;

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
            throw new ZenonRpcException('Unable to getPillarByName - ' . $e->getMessage());
        }
    }

    public function getPillarByOwner(string $address): PillarDTO
    {
        try {
            $data = $this->sdk->pillar->getByOwner($address)['data'][0];

            return PillarDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getPillarByOwner - ' . $e->getMessage());
        }
    }
}
