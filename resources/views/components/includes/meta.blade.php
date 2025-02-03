{!! MetaTags::renderTags() !!}

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="color-scheme" content="dark light">

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v={{ app_version_number() }}">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v={{ app_version_number() }}">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v={{ app_version_number() }}">
<link rel="manifest" href="/site.webmanifest?v={{ app_version_number() }}">
<link rel="mask-icon" href="/safari-pinned-tab.svg?v={{ app_version_number() }}" color="#4277ff">
<link rel="shortcut icon" href="/favicon.ico?v={{ app_version_number() }}">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="colours" content="{{ json_encode(config('zenon-hub.colours')) }}">
