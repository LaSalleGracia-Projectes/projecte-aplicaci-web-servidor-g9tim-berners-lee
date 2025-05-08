<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="auth-token" content="{{ session('auth_token') }}">
  @yield('head')
  <title>@yield('title', 'CritFlix')</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/randomizer.css') }}">
  @stack('styles')
  <script>
    window.TMDB_CONFIG = {
      API_KEY: "{{ config('tmdb.api_key') }}",
      BASE_URL: "{{ config('tmdb.base_url') }}",
      IMG_URL: "{{ config('tmdb.img_url') }}",
      BACKDROP_URL: "https://image.tmdb.org/t/p/original"
    };

    // Traducciones para JavaScript
    window.translations = {
      generating_recommendation: "{{ __('messages.generating_recommendation') }}",
      no_results_found: "{{ __('messages.no_results_found') }}",
      try_random_recommendation: "{{ __('messages.try_random_recommendation') }}",
      error_generating: "{{ __('messages.error_generating') }}",
      try_other_filters: "{{ __('messages.try_other_filters') }}",
      available_on_subscription: "{{ __('messages.available_on_subscription') }}",
      available_for_rent: "{{ __('messages.available_for_rent') }}",
      available_for_purchase: "{{ __('messages.available_for_purchase') }}",
      see_all_options: "{{ __('messages.see_all_options') }}",
      where_to_watch: "{{ __('messages.where_to_watch') }}",
      main_cast: "{{ __('messages.main_cast') }}",
      production: "{{ __('messages.production') }}",
      watch_trailer: "{{ __('messages.watch_trailer') }}",
      search_trailer: "{{ __('messages.search_trailer') }}",
      new_recommendation: "{{ __('messages.new_recommendation') }}",
      no_results_in_platform: "{{ __('messages.no_results_in_platform') }}",
      try_again: "{{ __('messages.try_again') }}",
      try_without_platform: "{{ __('messages.try_without_platform') }}",
      // Idiomas
      lang_en: "{{ __('messages.lang_en') }}",
      lang_es: "{{ __('messages.lang_es') }}",
      lang_fr: "{{ __('messages.lang_fr') }}",
      lang_de: "{{ __('messages.lang_de') }}",
      lang_it: "{{ __('messages.lang_it') }}",
      lang_ja: "{{ __('messages.lang_ja') }}",
      lang_ko: "{{ __('messages.lang_ko') }}",
      lang_ru: "{{ __('messages.lang_ru') }}",
      lang_zh: "{{ __('messages.lang_zh') }}",
      lang_hi: "{{ __('messages.lang_hi') }}",
      lang_pt: "{{ __('messages.lang_pt') }}",
      lang_ar: "{{ __('messages.lang_ar') }}",
      lang_ca: "{{ __('messages.lang_ca') }}"
    };
  </script>
  <script type="module" src="{{ asset('js/main.js') }}"></script>
</head>
<body>
  @include('partials.header')
  @include('partials.modals')

  <!-- Notificaciones flash -->
  @if(session('success'))
  <div id="flash-message" class="toast toast-success show">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
  </div>
  <script>
    // Auto-cerrar el mensaje después de 3 segundos
    setTimeout(function() {
      const flashMessage = document.getElementById('flash-message');
      if (flashMessage) {
        flashMessage.classList.remove('show');
        setTimeout(function() {
          flashMessage.remove();
        }, 300);
      }
    }, 3000);
  </script>
  @endif

  @yield('content')

  @include('partials.back-to-top')
  @include('partials.footer')
  @stack('scripts')

  <!-- Yield para scripts específicos de cada página -->
  @yield('scripts')
</body>
</html>

