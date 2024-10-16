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

    public function send(string $toAddress, int $amount): bool
    {
        $result = $this->runCommand("send {$toAddress} {$amount}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    public function receive(string $hash): bool
    {
        $result = $this->runCommand("receive {$hash}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    public function receiveAll(): bool
    {
        $result = $this->runCommand('receiveAll');

        if (! $result->seeInOutput('Done')) {
            return false;
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
        ])->filter()->implode(fn ($value, $key) => "-{$key} $value ");
        $flags = trim($flags);

        $command = "./znn-cli {$action} {$flags}";
        $result = Process::path($path)->run($command);

        dd($result->command());

        if (! $result->successful()) {
            Log::error($result->errorOutput());
            throw new ZenonCliException($result->errorOutput());
        }

        return $result;
    }
}
