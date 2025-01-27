<?php

declare(strict_types=1);

namespace App\Actions\PlasmaBot;

use App\Models\PlasmaBotEntry;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class CancelExpired
{
    use AsAction;

    public string $commandSignature = 'plasma-bot:cancel-expired';

    public function handle(): void
    {
        $expiredEntries = PlasmaBotEntry::whereExpired()->whereConfirmed()->get();
        $expiredEntries->each(function ($entry) {
            try {
                Cancel::run($entry);
            } catch (Throwable $e) {
                Log::error('Plasma Bot - Error canceling expired entry', [
                    'entry' => $entry,
                ]);
            }
        });
    }
}
