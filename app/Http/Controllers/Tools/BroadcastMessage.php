<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\PageController;

class BroadcastMessage extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Broadcast Message';
        $this->page['meta']['description'] = 'Send a signed and verified message to the community forum';
        $this->page['data'] = [
            'component' => 'tools.broadcast-message',
        ];
        return $this->render('pages/tools');
    }
}
