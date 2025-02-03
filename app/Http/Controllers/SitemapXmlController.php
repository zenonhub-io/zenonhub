<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class SitemapXmlController
{
    public function __invoke(): Response
    {
        $file = storage_path('app/sitemap/sitemap.xml');

        return response()->file($file, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
