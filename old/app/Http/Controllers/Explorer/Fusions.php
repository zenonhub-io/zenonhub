<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use Meta;

class Fusions
{
    public function show()
    {
        Meta::title('QSR (Plasma) Fusions')
            ->description('A list of all the addresses in the Zenon Network actively fusing QSR into plasma sorted by creation timestamp in descending order');

        return view('pages/explorer/overview', [
            'view' => 'explorer.fusions',
        ]);
    }
}
