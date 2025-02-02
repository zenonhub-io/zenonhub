<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tools;

use Illuminate\Contracts\View\View;
use MetaTags;

class VerifySignatureController
{
    public function __invoke(): View
    {
        MetaTags::title('Verify Signed Message')
            ->description('Use the verification tool to check a signed messages signature is valid for the given address')
            ->meta([
                'robots' => 'index,follow',
                'canonical' => route('tools.verify-signature'),
            ]);

        return view('tools.verify-signature');
    }
}
