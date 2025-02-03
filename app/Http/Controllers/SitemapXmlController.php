<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class SitemapXmlController
{
    public function __invoke(): \Illuminate\Http\Response
    {
        $file = storage_path('app/sitemap/sitemap.xml');
        $content = file_get_contents($file);

        return Response::make($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
