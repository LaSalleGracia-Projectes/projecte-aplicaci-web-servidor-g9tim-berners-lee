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
        $this->applyLocale($locale);

        // Continuar con la petición
        return $next($request);
    }

    /**
     * Obtiene el idioma a utilizar basado en diferentes fuentes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getLocale(Request $request)
    {
        // 1. Verificar si viene como parámetro en la URL (?lang=xx)
        $locale = $request->query('lang');
        if ($locale && in_array($locale, $this->supportedLanguages)) {
            return $locale;
        }

        // 2. Verificar en la cookie
        $locale = $request->cookie('locale');
        if ($locale && in_array($locale, $this->supportedLanguages)) {
            return $locale;
        }

        // 3. Verificar en la sesión
        $locale = session('locale');
        if ($locale && in_array($locale, $this->supportedLanguages)) {
            return $locale;
        }

        // 4. Verificar en el navegador (cabecera Accept-Language)
        $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE') ?? '', 0, 2);
        if ($browserLocale && in_array($browserLocale, $this->supportedLanguages)) {
            return $browserLocale;
        }

        // 5. Si no se encuentra, usar el idioma por defecto
        return $this->defaultLanguage;
    }

    /**
     * Aplica el idioma a la aplicación
     *
     * @param  string  $locale
     * @return void
     */
    protected function applyLocale($locale)
    {
        // Establecer el idioma en la aplicación
        App::setLocale($locale);

        // Mantener el idioma en la sesión para futuras peticiones
        if (session('locale') !== $locale) {
            session()->put('locale', $locale);
        }
    }
}
