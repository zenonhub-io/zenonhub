<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tools;

use Meta;

class BroadcastMessage
{
    public function show()
    {
        Meta::title('Broadcast Message')
            ->description('Pillar owners can use the broadcasting tool to send a signed and verified message to the community forum');

        return view('pages/tools', [
            'view' => 'tools.broadcast-message',
        ]);
    }
}
