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
            ->description('Use the verification tool to check a signed messages signature is valid for the given address');

        return view('tools.verify-signature');
    }
}
