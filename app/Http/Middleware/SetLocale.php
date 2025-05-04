<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Intentar obtener el idioma de la cookie primero
        $locale = $request->cookie('locale');

        // Si no hay cookie, buscar en la sesión
        if (!$locale) {
            $locale = $request->session()->get('locale');
        }

        // Si no hay cookie ni sesión, usar el idioma por defecto
        if (!$locale || !in_array($locale, ['es', 'ca', 'en'])) {
            $locale = 'es'; // Idioma por defecto
        }

        // FORZAR el idioma para esta petición
        App::setLocale($locale);
        config(['app.locale' => $locale]);

        // Si no hay locale en sesión, guardarlo para mantener consistencia
        if (!$request->session()->has('locale')) {
            $request->session()->put('locale', $locale);
        }

        // Para depuración
        Log::info('SetLocale: FORCE-ENABLED ' . App::getLocale() .
            ' (Cookie: ' . ($request->cookie('locale') ?: 'no') .
            ', Session ID: ' . $request->session()->getId() . ')');

        return $next($request);
    }
}
