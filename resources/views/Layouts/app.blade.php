<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'CritFlix')</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
  <script>
    const API_KEY = "{{ config('tmdb.api_key') }}";
    const BASE_URL = "{{ config('tmdb.base_url') }}";
    const IMG_URL = "{{ config('tmdb.img_url') }}";
</script>
<script type="module" src="{{ asset('script.js') }}"></script>

</head>
<body>
  @include('partials.header')
  @include('partials.modals')

  @yield('content')

  @include('partials.back-to-top')
  @include('partials.footer')
</body>
</html>
