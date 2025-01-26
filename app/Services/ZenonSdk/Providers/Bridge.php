<?php

declare(strict_types=1);

namespace App\Services\ZenonSdk\Providers;

use App\DataTransferObjects\Nom\BridgeInfoDTO;
use App\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

trait Bridge
{
    public function getBridgeInfo(): BridgeInfoDTO
    {
        try {
            $data = $this->sdk->bridge->getBridgeInfo()['data'];

            return BridgeInfoDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getBridgeInfo - ' . $e->getMessage());
        }
    }

    public function getPendingIncoming(): int
    {
        try {
            $this->sdk->bridge->getAllUnsignedWrapTokenRequests()['data']['count'];

            return $this->sdk->bridge->getAllUnsignedWrapTokenRequests()['data']['count'];
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getPendingIncoming - ' . $e->getMessage());
        }
    }

    public function getPendingOutgoing(): int
    {
        // TODO
        try {
            return $this->sdk->bridge->getAllUnsignedWrapTokenRequests()['data']['count'];
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getPendingOutgoing - ' . $e->getMessage());
        }
    }
}
