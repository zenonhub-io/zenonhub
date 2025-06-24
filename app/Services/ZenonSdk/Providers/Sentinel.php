<?php

declare(strict_types=1);

namespace App\Services\ZenonSdk\Providers;

use App\DataTransferObjects\Nom\SentinelDTO;
use App\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Support\Collection;

trait Sentinel
{
    public function getActiveSentinels(int $page = 0, int $perPage = 1000): Collection
    {
        try {
            $data = $this->sdk->sentinel->getAllActive($page, $perPage)['data']->list;

            return SentinelDTO::collect($data, Collection::class);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getActiveSentinels - ' . $e->getMessage());
        }
    }

    public function getSentinelByOwner(string $address): SentinelDTO
    {
        try {
            $data = $this->sdk->sentinel->getByOwner($address)['data'];

            return SentinelDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getSentinelByOwner - ' . $e->getMessage());
        }
    }
}
