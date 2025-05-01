<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="auth-token" content="{{ session('auth_token') }}">
  <title>@yield('title', 'CritFlix')</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
  @stack('styles')
  <script>
    window.TMDB_CONFIG = {
      API_KEY: "{{ config('tmdb.api_key') }}",
      BASE_URL: "{{ config('tmdb.base_url') }}",
      IMG_URL: "{{ config('tmdb.img_url') }}",
      BACKDROP_URL: "https://image.tmdb.org/t/p/original"
    };
  </script>
  <script type="module" src="{{ asset('js/main.js') }}"></script>
</head>
<body>
  @include('partials.header')
  @include('partials.modals')

  @yield('content')

  @include('partials.back-to-top')
  @include('partials.footer')
  @stack('scripts')

  <!-- Yield para scripts específicos de cada página -->
  @yield('scripts')
</body>
</html>

