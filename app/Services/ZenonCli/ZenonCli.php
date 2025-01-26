<?php

declare(strict_types=1);

namespace App\Services\ZenonCli;

use App\Exceptions\ZenonCliException;
use App\Services\ZenonCli\Providers\Plasma;
use App\Services\ZenonCli\Providers\Wallet;
use Illuminate\Process\ProcessResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ZenonCli
{
    use Plasma, Wallet;

    public function __construct(
        protected ?string $executablePath,
        protected string $nodeUrl,
        protected ?string $keystore = null,
        protected ?string $passphrase = null,
    ) {}

    //
    // General

    public function setKeystore(string $keystore): void
    {
        $this->keystore = $keystore;
    }

    public function setPassphrase(string $passphrase): void
    {
        $this->passphrase = $passphrase;
    }

    public function setNodeUrl(string $nodeUrl): void
    {
        $this->nodeUrl = $nodeUrl;
    }

    public function send(string $toAddress, int $amount): bool
    {
        $result = $this->runCommand("send {$toAddress} {$amount}");

        if (! $result->seeInOutput('Done')) {
            throw new ZenonCliException('Zenon CLI - Unable to send');
        }

        return true;
    }

    public function receive(string $hash): bool
    {
        $result = $this->runCommand("receive {$hash}");

        if (! $result->seeInOutput('Done')) {
            throw new ZenonCliException('Zenon CLI - Unable to receive');
        }

        return true;
    }

    public function receiveAll(): bool
    {
        $result = $this->runCommand('receiveAll');

        if (! $result->seeInOutput('Done')) {
            throw new ZenonCliException('Zenon CLI - Unable to receive all');
        }

        return true;
    }

    //
    // Internal

    /**
     * @throws ZenonCliException
     */
    protected function runCommand(string $action): ProcessResult
    {
        $path = base_path($this->executablePath);
        $flags = collect([
            'u' => $this->nodeUrl,
            'k' => $this->keystore,
            'p' => $this->passphrase,
            'c' => app()->isProduction() ? '1' : '3',
        ])->filter()->implode(fn ($value, $key) => "-{$key} $value ");
        $flags = trim($flags);

        $command = "./znn-cli {$action} {$flags}";
        $result = Process::path($path)->run($command);

        if (! $result->successful()) {
            Log::error($result->errorOutput());
            throw new ZenonCliException($result->errorOutput());
        }

        return $result;
    }
}
