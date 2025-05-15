<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="auth-token" content="{{ session('auth_token') }}">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <meta name="theme-color" content="#000000">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  @yield('head')
  <title>@yield('title', 'CritFlix')</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('styles.css') }}?{{ time() }}">
  <link rel="stylesheet" href="{{ asset('css/randomizer.css') }}?{{ time() }}">
  <link rel="stylesheet" href="{{ asset('language-fix.css') }}?{{ time() }}">
  @stack('styles')
  <script>
    // Información sobre el idioma actual
    window.APP_INFO = {
      currentLocale: "{{ app()->getLocale() }}",
      defaultLocale: "es"
    };

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
      lang_ca: "{{ __('messages.lang_ca') }}",
      // Nuevas traducciones para películas y series
      new_badge: "{{ __('messages.new_badge') }}",
      new_badge_female: "{{ __('messages.new_badge_female') }}",
      view_trailer: "{{ __('messages.view_trailer') }}",
      add_to_favorites: "{{ __('messages.add_to_favorites') }}",
      remove_from_favorites: "{{ __('messages.remove_from_favorites') }}",
      view_details: "{{ __('messages.view_details') }}",
      more_info: "{{ __('messages.more_info') }}",
      released: "{{ __('messages.released') }}",
      post_production: "{{ __('messages.post_production') }}",
      in_production: "{{ __('messages.in_production') }}",
      planned: "{{ __('messages.planned') }}",
      unknown: "{{ __('messages.unknown') }}",
      ended: "{{ __('messages.ended') }}",
      returning_series: "{{ __('messages.returning_series') }}",
      canceled: "{{ __('messages.canceled') }}",
      data_not_available: "{{ __('messages.data_not_available') }}",
      no_description: "{{ __('messages.no_description') }}",
      unknown_date: "{{ __('messages.unknown_date') }}",
      error_loading_details: "{{ __('messages.error_loading_details') }}",
      error_loading_comments: "{{ __('messages.error_loading_comments') }}",
      error_loading_responses: "{{ __('messages.error_loading_responses') }}",
      no_reviews_yet: "{{ __('messages.no_reviews_yet') }}",
      sending: "{{ __('messages.sending') }}",
      cancel_reply: "{{ __('messages.cancel_reply') }}",
      reply_to_comment: "{{ __('messages.reply_to_comment') }}",
      favorite: "{{ __('messages.favorite') }}",
      loading: "{{ __('messages.loading') }}",
      load_more_series: "{{ __('messages.load_more_series') }}",
      load_more_movies: "{{ __('messages.load_more_movies') }}",
      no_title: "{{ __('messages.data_not_available') }}",
      loading_details: "{{ __('messages.loading_details') }}",
      loading_trailer: "{{ __('messages.loading_trailer') }}",
      back_to_top: "{{ __('messages.back_to_top') }}",
      showing: "{{ __('messages.showing') }}",
      results: "{{ __('messages.results') }}",
      error_loading_movies: "{{ __('messages.error_loading_movies') }}",
      error_message: "{{ __('messages.error_notification') }}",
      retry: "{{ __('messages.retry') }}",
      error_loading_series: "{{ __('messages.error_loading_series') }}",
      error_loading_trailer: "{{ __('messages.error_loading_trailer') }}",
      no_trailer_available: "{{ __('messages.no_trailer_available') }}",
      loading_comments: "{{ __('messages.loading_comments') }}",
      loading_responses: "{{ __('messages.loading_responses') }}",
      loading_favorites: "{{ __('messages.loading_favorites') }}",
      budget: "{{ __('messages.budget') }}",
      not_available: "{{ __('messages.not_available') }}",
      sinopsis: "{{ __('messages.sinopsis') }}",
      no_trailer: "{{ __('messages.no_trailer') }}",
      similar_movies: "{{ __('messages.similar_movies') }}",
      similar_series: "{{ __('messages.similar_series') }}",
      see_full_page: "{{ __('messages.see_full_page') }}",
      role_director: "{{ __('messages.role_director') }}",
      role_writers: "{{ __('messages.role_writers') }}",
      role_writers_plural: "{{ __('messages.role_writers_plural') }}",
      role_creator: "{{ __('messages.role_creator') }}",
      role_creators: "{{ __('messages.role_creators') }}",
      role_showrunner: "{{ __('messages.role_showrunner') }}",
      network: "{{ __('messages.network') }}",
      production_company: "{{ __('messages.production_company') }}",
      episodes: "{{ __('messages.episodes') }}",
    };
  </script>
  <script type="module" src="{{ asset('js/main.js') }}?{{ time() }}"></script>
</head>
<body>
  <div class="page-wrapper">
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

    <main class="main-content">
      @yield('content')
    </main>

    @include('partials.back-to-top')
    @include('partials.footer')
  </div>

  @stack('scripts')

  <!-- Yield para scripts específicos de cada página -->
  @yield('scripts')
</body>
</html>

