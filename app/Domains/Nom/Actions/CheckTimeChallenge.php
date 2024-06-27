<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\TimeChallenge;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckTimeChallenge
{
    use AsAction;

    public function handle(AccountBlock $accountBlock, string $hash, int $delay): TimeChallenge
    {
        $momentum = $accountBlock->momentum;
        $timeChallenge = TimeChallenge::firstOrCreate([
            'chain_id' => app('currentChain')->id,
            'contract_method_id' => $accountBlock->contract_method_id,
            'delay' => $delay,
            'is_active' => true,
        ]);

        if (! $timeChallenge->start_height) {
            $timeChallenge->start_height = $momentum->height;
            $timeChallenge->end_height = $timeChallenge->start_height + $timeChallenge->delay;
            $timeChallenge->created_at = $momentum->created_at;
            $timeChallenge->save();
        }

        $hash = md5($hash);

        if ($timeChallenge->hash === $hash) {
            if ($momentum->height >= $timeChallenge->end_height) {
                $timeChallenge->is_active = false;
                $timeChallenge->save();
            }
        } else {
            $timeChallenge->hash = $hash;
            $timeChallenge->start_height = $momentum->height;
            $timeChallenge->end_height = $timeChallenge->start_height + $timeChallenge->delay;
            $timeChallenge->save();
        }

        return $timeChallenge;
    }
}
