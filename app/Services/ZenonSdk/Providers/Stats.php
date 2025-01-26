<?php

declare(strict_types=1);

namespace App\Services\ZenonSdk\Providers;

use App\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

trait Stats
{
    public function getOsInfo(): ?object
    {
        try {
            return $this->sdk->stats->osInfo()['data'];
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to osInfo - ' . $e->getMessage());
        }
    }

    public function getRuntimeInfo(): ?object
    {
        try {
            return $this->sdk->stats->runtimeInfo()['data'];
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to runtimeInfo - ' . $e->getMessage());
        }
    }

    public function getProcessInfo(): ?object
    {
        try {
            return $this->sdk->stats->processInfo()['data'];
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to processInfo - ' . $e->getMessage());
        }
    }

    public function getSyncInfo(): ?object
    {
        try {
            return $this->sdk->stats->syncInfo()['data'];
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to syncInfo - ' . $e->getMessage());
        }
    }

    public function getNetworkInfo(): ?object
    {
        try {
            return $this->sdk->stats->networkInfo()['data'];
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to networkInfo - ' . $e->getMessage());
        }
    }
}
