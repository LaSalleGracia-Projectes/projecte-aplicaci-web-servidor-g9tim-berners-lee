<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('files', function () {
            return new Filesystem();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configuración de idioma en cada solicitud
        $this->configureLocale();
    }

    /**
     * Configura el idioma de la aplicación basado en la sesión o la cookie.
     */
    protected function configureLocale(): void
    {
        // Solo si estamos en una petición web
        if (!$this->app->runningInConsole()) {
            $request = request();

            // Determinar el idioma a utilizar
            $locale = null;

            // 1. Verificar si hay un parámetro 'lang' en la URL
            if ($request->has('lang')) {
                $locale = $request->query('lang');
            }

            // 2. Verificar si hay una cookie 'locale'
            if (!$locale && $request->hasCookie('locale')) {
                $locale = $request->cookie('locale');
            }

            // 3. Verificar si hay un valor en la sesión
            if (!$locale && session()->has('locale')) {
                $locale = session('locale');
            }

            // Validar que el idioma es soportado
            if (!$locale || !in_array($locale, ['es', 'ca', 'en'])) {
                $locale = Config::get('app.locale', 'es');
            }

            // Aplicar el idioma a nivel global
            App::setLocale($locale);

            // Guardar en la sesión para futuras peticiones
            if (session('locale') !== $locale) {
                session(['locale' => $locale]);
            }
        }
    }
}
