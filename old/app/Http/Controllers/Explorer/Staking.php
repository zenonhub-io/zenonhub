<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use Meta;

class Staking
{
    public function show()
    {
        Meta::title('Staking')
            ->description('A list of all staking entries for ZNN and ETH LP tokens on the Zenon Network, displayed by start timestamp in descending order');

        return view('pages/explorer/overview', [
            'view' => 'explorer.staking',
        ]);
    }
}
