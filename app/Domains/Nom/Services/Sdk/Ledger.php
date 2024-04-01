<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services\Sdk;

use App\Domains\Nom\DataTransferObjects\AccountBlockData;
use App\Domains\Nom\DataTransferObjects\MomentumData;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Support\Collection;

trait Ledger
{
    /**
     * @throws ZenonRpcException
     */
    public function getFrontierMomentum(): MomentumData
    {
        try {
            $data = $this->sdk->ledger->getFrontierMomentum()['data'];

            return MomentumData::from($data);
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

            return MomentumData::collect($data, Collection::class);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getMomentumsByHeight');
        }
    }

    /**
     * @throws ZenonRpcException
     */
    public function getFrontierAccountBlock(string $address): ?AccountBlockData
    {
        try {
            $data = $this->sdk->ledger->getFrontierAccountBlock($address)['data'];

            if (! $data) {
                return null;
            }

            return AccountBlockData::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getFrontierAccountBlock');
        }
    }

    /**
     * @throws ZenonRpcException
     */
    public function getAccountBlockByHash(string $hash): AccountBlockData
    {
        try {
            $data = $this->sdk->ledger->getAccountBlockByHash($hash)['data'];

            return AccountBlockData::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getAccountBlockByHash');
        }
    }
}
