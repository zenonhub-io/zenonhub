<?php

declare(strict_types=1);

namespace App\Services\ZenonSdk\Providers;

use App\DataTransferObjects\Nom\TokenDTO;
use App\Exceptions\ZenonRpcException;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

trait Token
{
    /**
     * @throws ZenonRpcException
     */
    public function getByZts($zts): TokenDTO
    {
        try {
            $data = $this->sdk->token->getByZts($zts)['data'];

            return TokenDTO::from($data);
        } catch (Exception $e) {
            throw new ZenonRpcException('Unable to getByZts - ' . $e->getMessage());
        }
    }
}
