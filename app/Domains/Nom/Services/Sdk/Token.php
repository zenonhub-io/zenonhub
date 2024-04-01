<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services\Sdk;

use App\Domains\Nom\DataTransferObjects\TokenData;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

trait Token
{
    /**
     * @throws ZenonRpcException
     */
    public function getByZts($zts): TokenData
    {
        try {
            $data = $this->sdk->token->getByZts($zts)['data'];

            return TokenData::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getFrontierMomentum');
        }
    }
}
