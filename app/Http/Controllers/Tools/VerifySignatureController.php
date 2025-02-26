<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tools;

use Illuminate\Contracts\View\View;
use MetaTags;

class VerifySignatureController
{
    public function __invoke(): View
    {
        MetaTags::title('Verify Signature: Check Signed Message Authenticity')
            ->description('Use the verification tool to ensure a signed message\'s signature is valid for the provided address')
            ->canonical(route('tools.verify-signature'))
            ->metaByName('robots', 'index,nofollow');

        return view('tools.verify-signature');
    }
}
