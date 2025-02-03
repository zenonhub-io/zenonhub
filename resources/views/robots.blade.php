User-agent: *

Disallow: /admin/
Disallow: /horizon/
Disallow: /api/
Disallow: /timezone/

Disallow: /explorer/momentum/*
Disallow: /explorer/transaction/*
Disallow: /explorer/account/*
Disallow: /explorer/token/*
Disallow: /profile/*

@foreach ($allowedAccounts as $account)
Allow: /explorer/account/{{ $account }}
@endforeach

@foreach ($allowedTokens as $token)
Allow: /explorer/token/{{ $token }}
@endforeach

Disallow: /*?*

Allow: /

Sitemap: {{ $sitemap }}
