<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\File;

class DiagnosticController extends Controller
{
    /**
     * Mostrar un diagnóstico completo del sistema de traducciones
     */
    public function index(Request $request)
    {
        // Información básica sobre el idioma
        $locale = App::getLocale();
        $sessionLocale = session('locale', 'no hay');
        $cookieLocale = $request->cookie('locale') ?? 'no hay';

        // Información sobre archivos de traducción
        $translations = [];
        $langPath = resource_path('lang');

        // Verificar si el directorio de idiomas existe
        if (File::isDirectory($langPath)) {
            // Obtener todos los idiomas disponibles
            $languages = File::directories($langPath);

            foreach ($languages as $langDir) {
                $lang = basename($langDir);
                $files = File::files($langDir);

                $translations[$lang] = [
                    'directory' => $langDir,
                    'files' => [],
                ];

                foreach ($files as $file) {
                    $filename = basename($file);
                    $key = str_replace('.php', '', $filename);

                    // Cargar algunas traducciones de prueba
                    $testKeys = [
                        'home', 'movies', 'series', 'login', 'register',
                        'language', 'spanish', 'english', 'catalan'
                    ];

                    $examples = [];
                    foreach ($testKeys as $testKey) {
                        $fullKey = "$key.$testKey";
                        $examples[$testKey] = Lang::has($fullKey) ? Lang::get($fullKey) : 'no existe';
                    }

                    $translations[$lang]['files'][] = [
                        'name' => $filename,
                        'path' => $file,
                        'examples' => $examples
                    ];
                }
            }
        }

        // Información sobre configuración de Laravel
        $appConfig = [
            'app.locale' => config('app.locale'),
            'app.fallback_locale' => config('app.fallback_locale'),
            'session.driver' => config('session.driver'),
            'session.lifetime' => config('session.lifetime'),
        ];

        // Prueba de traducción en vivo
        $liveTests = [];

        // Prueba con el helper __()
        $liveTests['helper'] = [
            'home' => __('messages.home'),
            'movies' => __('messages.movies'),
            'series' => __('messages.series'),
            'login' => __('messages.login')
        ];

        // Prueba con trans()
        $liveTests['trans'] = [
            'home' => trans('messages.home'),
            'movies' => trans('messages.movies'),
            'series' => trans('messages.series'),
            'login' => trans('messages.login')
        ];

        return view('diagnostics.language', compact(
            'locale',
            'sessionLocale',
            'cookieLocale',
            'translations',
            'appConfig',
            'liveTests'
        ));
    }

    /**
     * Cambiar temporalmente el idioma para probar traducciones
     * sin afectar la configuración actual
     */
    public function testTranslation(Request $request, $locale)
    {
        $testLocale = in_array($locale, ['es', 'ca', 'en']) ? $locale : 'es';

        // La clave a traducir
        $key = $request->input('key', 'messages.home');

        // Obtener la traducción en el idioma solicitado
        $translation = Lang::get($key, [], $testLocale);

        return response()->json([
            'locale' => $testLocale,
            'key' => $key,
            'translation' => $translation
        ]);
    }
}
