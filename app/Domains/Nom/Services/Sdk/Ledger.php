<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services\Sdk;

use App\Domains\Nom\DataTransferObjects\AccountBlockDTO;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Support\Collection;

trait Ledger
{
    /**
     * @throws ZenonRpcException
     */
    public function getFrontierMomentum(): MomentumDTO
    {
        try {
            $data = $this->sdk->ledger->getFrontierMomentum()['data'];

            return MomentumDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getFrontierMomentum');
        }
    }

    /**
     * @throws ZenonRpcException
     */
    public function getMomentumsByHeight(int $height, int $count = 100): Collection
    {
        try {
            $data = $this->sdk->ledger->getMomentumsByHeight($height, $count)['data']->list;

            return MomentumDTO::collect($data, Collection::class);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getMomentumsByHeight');
        }
    }

    /**
     * @throws ZenonRpcException
     */
    public function getMomentumsByHash(string $hash): MomentumDTO
    {
        try {
            $data = $this->sdk->ledger->getMomentumByHash($hash)['data'];

            return MomentumDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getMomentumsByHeight');
        }
    }

    /**
     * @throws ZenonRpcException
     */
    public function getFrontierAccountBlock(string $address): ?AccountBlockDTO
    {
        try {
            $data = $this->sdk->ledger->getFrontierAccountBlock($address)['data'];

            if (! $data) {
                return null;
            }

            return AccountBlockDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getFrontierAccountBlock');
        }
    }

    /**
     * @throws ZenonRpcException
     */
    public function getAccountBlockByHash(string $hash): AccountBlockDTO
    {
        try {
            $data = $this->sdk->ledger->getAccountBlockByHash($hash)['data'];

            return AccountBlockDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getAccountBlockByHash');
        }
    }
}
