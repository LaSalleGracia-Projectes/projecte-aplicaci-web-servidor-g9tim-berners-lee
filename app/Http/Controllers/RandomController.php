<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeliculasSeries;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RandomController extends Controller
{
    // API URL base y clave API de TMDb
    private $apiBaseUrl = 'https://api.themoviedb.org/3';
    private $apiKey;

    public function __construct()
    {
        // Obtener la API key desde el archivo de configuración
        $this->apiKey = config('services.tmdb.api_key', env('TMDB_API_KEY'));
    }

    public function generate(Request $request)
    {
        // Log todos los parámetros recibidos
        Log::info('Parámetros recibidos:', $request->all());

        try {
            // Determinar el tipo de contenido (movie o tv)
            $mediaType = $request->filled('tipoContenido') ? $request->tipoContenido : 'movie';
            Log::info("Generando recomendación aleatoria para tipo: $mediaType");

            // Obtener el idioma actual de la aplicación
            $currentLocale = app()->getLocale();

            // Mapear locales de la aplicación a locales de TMDb
            $localeMap = [
                'es' => 'es-ES',
                'ca' => 'ca',
                'en' => 'en-US'
            ];

            // Determinar el idioma para TMDb, por defecto es español
            $apiLanguage = $localeMap[$currentLocale] ?? 'es-ES';

            Log::info("Usando idioma para API: {$apiLanguage} (desde locale: {$currentLocale})");

            // Construir los parámetros para la API
            $apiParams = [
                'api_key' => $this->apiKey,
                'language' => $apiLanguage,
                'include_adult' => false,
                'page' => rand(1, 5) // Para obtener resultados aleatorios, usamos páginas aleatorias
            ];

            // Añadir año si se especificó
            if ($request->filled('anio') && $request->anio !== '') {
                // Para películas usamos primary_release_year, para series first_air_date_year
                $yearParam = $mediaType === 'movie' ? 'primary_release_year' : 'first_air_date_year';
                $apiParams[$yearParam] = $request->anio;
                Log::info("Filtrando por año: {$request->anio}");
            }

            // Añadir género si se especificó
            if ($request->filled('genero') && $request->genero !== '') {
                // Mapeo de nombres de géneros a IDs de TMDb
                $generoId = $this->mapearGenero($request->genero, $mediaType);
                if ($generoId) {
                    $apiParams['with_genres'] = $generoId;
                    Log::info("Filtrando por género: {$request->genero} (ID: $generoId)");
                } else {
                    Log::info("Género no encontrado: {$request->genero}, omitiendo filtro");
                }
            }

            // Filtrar por duración (solo para películas)
            if ($mediaType === 'movie' && $request->filled('duracion') && $request->duracion !== '') {
                switch ($request->duracion) {
                    case 'short':
                        $apiParams['with_runtime.lte'] = 90;
                        Log::info("Filtrando por duración: < 90 min");
                        break;
                    case 'medium':
                        $apiParams['with_runtime.gte'] = 90;
                        $apiParams['with_runtime.lte'] = 120;
                        Log::info("Filtrando por duración: 90-120 min");
                        break;
                    case 'long':
                        $apiParams['with_runtime.gte'] = 120;
                        Log::info("Filtrando por duración: > 120 min");
                        break;
                }
            }

            // Hacer la solicitud a la API de TMDb
            $endpoint = $mediaType === 'movie' ? 'discover/movie' : 'discover/tv';
            $url = "{$this->apiBaseUrl}/{$endpoint}";

            Log::info("Realizando solicitud a: $url con parámetros:", $apiParams);

            $response = Http::get($url, $apiParams);

            if (!$response->successful()) {
                Log::error("Error en la respuesta de la API: " . $response->status());
                throw new \Exception("Error al consultar la API externa: " . $response->status());
            }

            $responseData = $response->json();

            // Verificar si hay resultados
            if (empty($responseData['results'])) {
                Log::warning("No se encontraron resultados con los filtros especificados");

                // Intentar nuevamente solo con el tipo de contenido
                Log::info("Reintentando solo con el tipo de contenido");

                $simpleParams = [
                    'api_key' => $this->apiKey,
                    'language' => $apiLanguage,
                    'include_adult' => false,
                    'page' => rand(1, 10) // Aumentamos el rango para tener más opciones
                ];

                $response = Http::get($url, $simpleParams);
                $responseData = $response->json();

                if (empty($responseData['results'])) {
                    return response()->json([
                        'message' => 'No se encontraron resultados con los criterios especificados',
                        'data' => null
                    ], 404);
                }
            }

            // Obtener un resultado aleatorio
            $results = $responseData['results'];
            $randomIndex = array_rand($results);
            $randomContent = $results[$randomIndex];

            Log::info("Contenido aleatorio encontrado: " . ($randomContent['title'] ?? $randomContent['name']));

            // Si es un resultado válido, obtener los detalles completos
            $detailsEndpoint = $mediaType === 'movie'
                ? "movie/{$randomContent['id']}"
                : "tv/{$randomContent['id']}";

            $detailsUrl = "{$this->apiBaseUrl}/{$detailsEndpoint}";
            $detailsParams = [
                'api_key' => $this->apiKey,
                'language' => $apiLanguage,
                'append_to_response' => 'credits,videos'
            ];

            $detailsResponse = Http::get($detailsUrl, $detailsParams);

            if ($detailsResponse->successful()) {
                $detailedContent = $detailsResponse->json();
                Log::info("Detalles obtenidos correctamente");

                // Combinar con datos adicionales que podríamos necesitar
                $randomContent = array_merge($randomContent, $detailedContent);
            } else {
                Log::warning("No se pudieron obtener detalles adicionales");
            }

            // Obtener información de plataformas de streaming
            $watchProvidersEndpoint = "{$this->apiBaseUrl}/{$mediaType}/{$randomContent['id']}/watch/providers";
            $watchProvidersParams = [
                'api_key' => $this->apiKey
            ];

            Log::info("Obteniendo información de plataformas de streaming para ID: {$randomContent['id']}");
            $watchProvidersResponse = Http::get($watchProvidersEndpoint, $watchProvidersParams);

            if ($watchProvidersResponse->successful()) {
                $providers = $watchProvidersResponse->json();
                Log::info("Información de plataformas obtenida correctamente");

                // Obtener proveedores para España o, si no hay, para US
                $randomContent['providers'] = null;

                if (isset($providers['results']['ES'])) {
                    $randomContent['providers'] = $providers['results']['ES'];
                    Log::info("Encontrados proveedores para España");
                } elseif (isset($providers['results']['US'])) {
                    $randomContent['providers'] = $providers['results']['US'];
                    Log::info("No hay proveedores para España, usando proveedores de US");
                } else {
                    Log::info("No se encontraron proveedores de streaming");
                }
            } else {
                Log::warning("No se pudo obtener información de plataformas de streaming");
            }

            // Añadir el tipo de medio
            $randomContent['media_type'] = $mediaType;

            // Agregar URL base para las imágenes
            $randomContent['base_image_url'] = 'https://image.tmdb.org/t/p/w500';

            return response()->json([
                'message' => 'Recomendación generada correctamente',
                'data' => $randomContent
            ]);
        } catch (\Exception $e) {
            Log::error('Error en RandomController: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Error al generar la recomendación: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Mapea nombres de géneros en español a IDs de TMDb
     */
    private function mapearGenero($genero, $mediaType)
    {
        $genero = strtolower($genero);

        // Mapa de géneros para películas
        $generosMovies = [
            'accion' => 28,
            'aventura' => 12,
            'animacion' => 16,
            'comedia' => 35,
            'crimen' => 80,
            'documental' => 99,
            'drama' => 18,
            'familiar' => 10751,
            'fantasia' => 14,
            'historia' => 36,
            'terror' => 27,
            'musica' => 10402,
            'misterio' => 9648,
            'romance' => 10749,
            'ciencia ficcion' => 878,
            'thriller' => 53,
            'belica' => 10752,
            'western' => 37
        ];

        // Mapa de géneros para series
        $generosTv = [
            'accion' => 10759, // Acción y Aventura
            'animacion' => 16,
            'comedia' => 35,
            'crimen' => 80,
            'documental' => 99,
            'drama' => 18,
            'familiar' => 10751,
            'infantil' => 10762,
            'misterio' => 9648,
            'noticias' => 10763,
            'reality' => 10764,
            'ciencia ficcion' => 10765, // Sci-Fi & Fantasy
            'soap' => 10766,
            'talk' => 10767,
            'guerra' => 10768, // War & Politics
            'western' => 37
        ];

        $mapaGeneros = $mediaType === 'movie' ? $generosMovies : $generosTv;

        // Buscar coincidencia exacta
        if (isset($mapaGeneros[$genero])) {
            return $mapaGeneros[$genero];
        }

        // Buscar coincidencia parcial
        foreach ($mapaGeneros as $key => $id) {
            if (strpos($genero, $key) !== false || strpos($key, $genero) !== false) {
                return $id;
            }
        }

        return null;
    }
}
