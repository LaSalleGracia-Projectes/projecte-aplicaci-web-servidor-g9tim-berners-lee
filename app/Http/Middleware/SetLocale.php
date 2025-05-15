<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    /**
     * Los idiomas soportados por la aplicación
     *
     * @var array
     */
    protected $supportedLanguages = ['es', 'ca', 'en'];

    /**
     * Idioma por defecto de la aplicación
     *
     * @var string
     */
    protected $defaultLanguage = 'es';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Determinar el idioma a utilizar
        $locale = $this->getLocale($request);

        // Aplicar el idioma a la aplicación
        $this->applyLocale($locale, $request);

        // Continuar con la petición
        $response = $next($request);

        // Si es necesario, establecer o refrescar la cookie de idioma en la respuesta
        if (!$request->hasCookie('locale') || $request->cookie('locale') !== $locale) {
            $response->cookie('locale', $locale, 60 * 24 * 365, '/', null, false, false, false, 'lax');
        }

        return $response;
    }

    /**
     * Obtiene el idioma a utilizar basado en diferentes fuentes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getLocale(Request $request)
    {
        // Registrar debug info
        $debugInfo = ['url' => $request->url()];

        // 1. Verificar si viene como parámetro en la URL (?lang=xx) - Máxima prioridad
        $locale = $request->query('lang');
        if ($locale && in_array($locale, $this->supportedLanguages)) {
            $debugInfo['source'] = 'URL parameter';
            $debugInfo['locale'] = $locale;
            Log::debug('Locale detected', $debugInfo);
            return $locale;
        }

        // 2. Verificar en la cookie
        $locale = $request->cookie('locale');
        if ($locale && in_array($locale, $this->supportedLanguages)) {
            $debugInfo['source'] = 'Cookie';
            $debugInfo['locale'] = $locale;
            Log::debug('Locale detected', $debugInfo);
            return $locale;
        }

        // 3. Verificar en la sesión
        $locale = Session::get('locale');
        if ($locale && in_array($locale, $this->supportedLanguages)) {
            $debugInfo['source'] = 'Session';
            $debugInfo['locale'] = $locale;
            Log::debug('Locale detected', $debugInfo);
            return $locale;
        }

        // 4. Verificar en el navegador (cabecera Accept-Language)
        $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE') ?? '', 0, 2);
        if ($browserLocale && in_array($browserLocale, $this->supportedLanguages)) {
            $debugInfo['source'] = 'Browser';
            $debugInfo['locale'] = $browserLocale;
            Log::debug('Locale detected', $debugInfo);
            return $browserLocale;
        }

        // 5. Si no se encuentra, usar el idioma por defecto
        $debugInfo['source'] = 'Default';
        $debugInfo['locale'] = $this->defaultLanguage;
        Log::debug('Using default locale', $debugInfo);
        return $this->defaultLanguage;
    }

    /**
     * Aplica el idioma a la aplicación
     *
     * @param  string  $locale
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function applyLocale($locale, $request)
    {
        // Establecer el idioma en la aplicación
        App::setLocale($locale);

        // Mantener el idioma en la sesión para futuras peticiones
        if (Session::get('locale') !== $locale) {
            Session::put('locale', $locale);
        }

        // Si el idioma viene desde la URL, asegurarnos de que se mantenga en futuras peticiones
        if ($request->query('lang') && $request->query('lang') === $locale) {
            Session::put('locale', $locale);
            Cookie::queue('locale', $locale, 60 * 24 * 365, '/', null, false, false, false, 'lax');
        }
    }
}
