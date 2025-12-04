<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title inertia>{{ config('app.name', 'Laravel') }}</title>

  <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  @routes
  @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
  @inertiaHead
</head>

<body>
  @if (!request()->is('admin') && !request()->is('admin/*'))
  <noscript>
    <div><img src="https://mc.yandex.ru/watch/105676095" style="position:absolute; left:-9999px;" alt="" /></div>
  </noscript>
  @endif
  @inertia
</body>

</html>