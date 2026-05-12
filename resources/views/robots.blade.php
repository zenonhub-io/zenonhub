User-agent: *

@if (is_hqz())
Disallow: /
@else
Disallow: /admin/
Disallow: /horizon/
Disallow: /api/
Disallow: /explorer/momentum/
Disallow: /explorer/block/
@foreach ($allowedAccounts as $address)
Allow: /explorer/account/{{ $address }}/
@endforeach
Disallow: /explorer/account/

Allow: /

Sitemap: {{ $sitemap }}
@endif
