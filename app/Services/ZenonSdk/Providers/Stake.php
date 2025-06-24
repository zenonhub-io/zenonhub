<?php

declare(strict_types=1);

namespace App\Services\ZenonSdk\Providers;

use App\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

trait Stake
{
    public function getStakeFrontierRewardByPage(string $address, int $page = 0, int $perPage = 100): array
    {
        try {
            $data = $this->sdk->stake->getFrontierRewardByPage($address, $page, $perPage)['data']->list;

            // TODO - add dedicated DTO?
            return $data;
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getStakeFrontierRewardByPage - ' . $e->getMessage());
        }
    }

    public function getStakeEntriesByAddress(string $address, int $page = 0, int $perPage = 100): array
    {
        try {
            $data = $this->sdk->stake->getEntriesByAddress($address, $page, $perPage)['data']->list;

            // TODO - add dedicated DTO?
            return $data;
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getStakeFrontierRewardByPage - ' . $e->getMessage());
        }
    }
}
