<?php

namespace App\Http\Controllers\Tools;

use Meta;

class VerifySignature
{
    public function show()
    {
        Meta::title('Verify Signed Message')
            ->description('Check a signed messages signature is valid for the given address');

        return view('pages/tools', [
            'view' => 'tools.verify-signature',
        ]);
    }
}
