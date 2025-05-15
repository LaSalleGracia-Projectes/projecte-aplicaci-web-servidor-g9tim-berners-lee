<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use Illuminate\Cookie\CookieJar;

class LanguageController extends Controller
{
    /**
     * Cambia el idioma de la aplicación
     *
     * @param  Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function change(Request $request, $locale)
    {
        // Verificar que el idioma sea válido
        if (!in_array($locale, ['es', 'ca', 'en'])) {
            $locale = 'es'; // Idioma por defecto
        }

        // Crear una cookie manualmente para evitar errores de formato
        $minutes = 60 * 24 * 365; // Un año
        $path = '/';
        $domain = null;
        $secure = false;
        $httpOnly = false;
        $sameSite = 'lax';

        // Guardar el idioma en la sesión y establecer el locale
        Session::put('locale', $locale);
        App::setLocale($locale);

        // Crear cookie
        $cookie = cookie('locale', $locale, $minutes, $path, $domain, $secure, $httpOnly, false, $sameSite);

        // Preparar URL de redirección
        $referer = $request->headers->get('referer');
        $redirectUrl = $referer ?: route('home');

        // Mensaje dependiendo del idioma
        $messages = [
            'es' => 'Idioma cambiado a Español',
            'ca' => 'Idioma canviat a Català',
            'en' => 'Language changed to English'
        ];

        // Para peticiones AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $messages[$locale],
                'locale' => $locale,
                'redirect' => $redirectUrl
            ])->cookie($cookie);
        }

        // Agregar timestamp para forzar recarga completa
        $timestamp = time();
        // Eliminar cualquier parámetro de idioma previo
        $urlParts = parse_url($redirectUrl);
        $path = $urlParts['path'] ?? '';
        $query = isset($urlParts['query']) ? '?' . $urlParts['query'] : '';

        // Reconstruir la URL limpiando parámetros de idioma antiguos
        if (!empty($query)) {
            parse_str(substr($query, 1), $queryParams);
            unset($queryParams['lang']);
            unset($queryParams['t']);
            $query = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
        }

        // Añadir los nuevos parámetros
        $separator = empty($query) ? '?' : '&';
        $redirectUrl = $path . $query . "{$separator}lang={$locale}&t={$timestamp}";

        // Redirección normal con cookie y mensaje flash
        return Redirect::to($redirectUrl)
            ->cookie($cookie)
            ->with('success', $messages[$locale]);
    }
}
