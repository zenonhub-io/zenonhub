User-agent: *

@if (is_hqz())
    Disallow: *
@else
    Disallow: /admin/
    Disallow: /horizon/
    Disallow: /api/

    Allow: /

    Sitemap: {{ $sitemap }}
@endif
