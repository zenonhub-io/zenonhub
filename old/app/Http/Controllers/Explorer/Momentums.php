<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Domains\Nom\Models\Momentum;
use Meta;

class Momentums
{
    public function show()
    {
        Meta::title('Zenon Network Momentums (blocks)')
            ->description('A list of the latest confirmed Momentums on the Zenon Network. The timestamp, producer, number of transactions and hash are shown in the list');

        return view('pages/explorer/overview', [
            'view' => 'explorer.momentums',
        ]);
    }

    public function detail($hash)
    {
        $momentum = Momentum::findBy('hash', $hash);

        if (! $momentum) {
            abort(404);
        }

        Meta::title("Zenon Momentum #{$momentum->height}")
            ->description("Zenon Momentum Height {$momentum->height}. Detailed momentum info showing the network height, producing pillar and a list of transactions");

        return view('pages/explorer/detail', [
            'view' => 'explorer.momentum',
            'momentum' => $momentum,
        ]);
    }
}
