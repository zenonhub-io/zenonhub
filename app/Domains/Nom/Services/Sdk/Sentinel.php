<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services\Sdk;

use App\Domains\Nom\DataTransferObjects\SentinelDTO;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

trait Sentinel
{
    public function getSentinelByOwner(string $address): SentinelDTO
    {
        try {
            $data = $this->sdk->sentinel->getByOwner($address)['data'];

            return SentinelDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getByOwner');
        }
    }
}
