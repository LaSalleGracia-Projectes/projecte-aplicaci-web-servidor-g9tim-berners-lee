<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Config;

class LanguageController extends Controller
{
    /**
     * Cambia el idioma de la aplicación
     *
     * @param  Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function change(Request $request, $locale)
    {
        // Verificar que el idioma sea válido
        if (!in_array($locale, ['es', 'ca', 'en'])) {
            $locale = 'es'; // Idioma por defecto
        }

        // Guardar el idioma en la sesión - MÉTODO DIRECTO
        $request->session()->put('locale', $locale);

        // Borrar cualquier dato de sesión que pudiera estar causando problemas
        $request->session()->forget('_previous');

        // Forzar idioma en config
        config(['app.locale' => $locale]);

        // Guardar el idioma también en una cookie para mayor persistencia
        // Cookie con SameSite=Lax para mayor compatibilidad
        $cookie = cookie('locale', $locale, 60*24*365, '/', null, false, false, false, 'lax');

        // Actualizar el idioma de la aplicación de inmediato
        App::setLocale($locale);

        // Para depuración
        Log::info('CAMBIO DE IDIOMA: ' . $locale);
        Log::info('Session ID: ' . $request->session()->getId());
        Log::info('Cookie Domain: ' . config('session.domain', 'no domain'));
        Log::info('Idioma de la aplicación después del cambio: ' . App::getLocale());
        Log::info('Valor de la sesión después del cambio: ' . Session::get('locale'));

        // Guardar un mensaje de confirmación
        $languages = [
            'es' => 'Español',
            'ca' => 'Catalán',
            'en' => 'Inglés'
        ];

        // Cambiar idioma y redirigir con cookie y mensaje
        return redirect()->back()
            ->withCookie($cookie)
            ->with('success', 'Idioma cambiado a ' . $languages[$locale]);
    }
}
