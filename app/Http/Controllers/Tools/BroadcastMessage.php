<?php

namespace App\Http\Controllers\Tools;

use Meta;

class BroadcastMessage
{
    public function show()
    {
        Meta::title('Broadcast Message')
            ->description('Send a signed and verified message to the community forum');

        return view('pages/tools', [
            'view' => 'tools.broadcast-message',
        ]);
    }
}
