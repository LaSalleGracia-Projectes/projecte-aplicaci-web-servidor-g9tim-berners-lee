<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelicula;
use App\Models\ValoracionPelicula;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SeriesController extends Controller
{
    protected $apiKey;
    protected $imageBaseUrl;

    public function __construct()
    {
        $this->apiKey = 'ba232569da1aac2f9b80a35300d0b04f';
        $this->imageBaseUrl = 'https://image.tmdb.org/t/p/';
    }

    /**
     * Muestra la página principal de series.
     */
    public function index()
    {
        // No necesitamos cargar series iniciales aquí,
        // ya que lo hace el JavaScript dinámicamente mediante la API de TMDB
        return view('series');
    }

    /**
     * Muestra los detalles de una serie específica.
     */
    public function show($id)
    {
        try {
            // Intentar obtener datos desde la API de TMDB
            $response = Http::get("https://api.themoviedb.org/3/tv/{$id}", [
                'api_key' => $this->apiKey,
                'language' => 'es-ES',
                'append_to_response' => 'credits,videos,similar,recommendations'
            ]);

            if ($response->successful()) {
                // Procesar datos para la vista
                $serie = $this->procesarDatosSerie($response->json());

                // Preparar datos adicionales
                $elenco = isset($response->json()['credits']['cast']) ? $response->json()['credits']['cast'] : [];
                $temporadas = isset($response->json()['seasons']) ? $response->json()['seasons'] : [];
                $seriesSimilares = isset($response->json()['similar']['results']) ? $response->json()['similar']['results'] : [];

                // Obtener proveedores de streaming si están disponibles
                $watchProvidersResponse = Http::get("https://api.themoviedb.org/3/tv/{$id}/watch/providers", [
                    'api_key' => $this->apiKey
                ]);

                $watchProviders = [];
                if ($watchProvidersResponse->successful()) {
                    $watchProvidersData = $watchProvidersResponse->json();
                    $watchProviders = isset($watchProvidersData['results']['ES']) ? $watchProvidersData['results']['ES'] : [];
                }

                return view('infoSerie', [
                    'serie' => $serie,
                    'elenco' => $elenco,
                    'temporadas' => $temporadas,
                    'seriesSimilares' => $seriesSimilares,
                    'watchProviders' => $watchProviders
                ]);
            } else {
                return view('error', [
                    'message' => 'No se pudo encontrar la serie solicitada.'
                ]);
            }
        } catch (\Exception $e) {
            return view('error', [
                'message' => 'Ocurrió un error al cargar los datos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Procesa los datos de la serie para la vista.
     */
    private function procesarDatosSerie($data)
    {
        $serie = (object) [
            'id' => $data['id'],
            'titulo' => $data['name'],
            'titulo_original' => $data['original_name'],
            'sinopsis' => $data['overview'] ?: 'No hay sinopsis disponible.',
            'poster_url' => $data['poster_path']
                ? $this->imageBaseUrl . 'w500' . $data['poster_path']
                : '/images/no-poster.jpg',
            'backdrop_url' => $data['backdrop_path']
                ? $this->imageBaseUrl . 'original' . $data['backdrop_path']
                : '/images/no-backdrop.jpg',
            'fecha_estreno' => isset($data['first_air_date']) ? date('d/m/Y', strtotime($data['first_air_date'])) : 'Desconocida',
            'first_air_date' => $data['first_air_date'] ?? null,
            'generos' => isset($data['genres']) ? array_column($data['genres'], 'name') : [],
            'puntuacion' => $data['vote_average'] * 10,
            'vote_average' => $data['vote_average'] ?? 0,
            'popularidad' => $data['popularity'],
            'number_of_seasons' => $data['number_of_seasons'] ?? 0,
            'number_of_episodes' => $data['number_of_episodes'] ?? 0,
            'in_production' => $data['in_production'] ?? false,
            'status' => $data['status'] ?? '',
            'episode_run_time' => $data['episode_run_time'] ?? [],
            'networks' => $data['networks'] ?? [],
            'origin_country' => $data['origin_country'] ?? [],
            'production_companies' => $data['production_companies'] ?? [],
            'created_by' => $data['created_by'] ?? [],
            'genres' => $data['genres'] ?? [],
            'videos' => isset($data['videos']) ? $data['videos'] : null,
            'tagline' => $data['tagline'] ?? '',
            'last_air_date' => $data['last_air_date'] ?? null,
            'overview' => $data['overview'] ?? 'No hay sinopsis disponible.',
            'name' => $data['name'] ?? '',
            'original_name' => $data['original_name'] ?? '',
            'poster_path' => $data['poster_path'] ?? null,
            'ultimo_episodio' => isset($data['last_episode_to_air'])
                ? [
                    'nombre' => $data['last_episode_to_air']['name'],
                    'temporada' => $data['last_episode_to_air']['season_number'],
                    'episodio' => $data['last_episode_to_air']['episode_number'],
                    'fecha' => date('d/m/Y', strtotime($data['last_episode_to_air']['air_date']))
                ]
                : null,
            'proximo_episodio' => isset($data['next_episode_to_air'])
                ? [
                    'nombre' => $data['next_episode_to_air']['name'],
                    'temporada' => $data['next_episode_to_air']['season_number'],
                    'episodio' => $data['next_episode_to_air']['episode_number'],
                    'fecha' => date('d/m/Y', strtotime($data['next_episode_to_air']['air_date']))
                ]
                : null
        ];

        // Procesar reparto si existe
        if (isset($data['credits']) && isset($data['credits']['cast'])) {
            $serie->reparto = array_map(function ($actor) {
                return [
                    'nombre' => $actor['name'],
                    'personaje' => $actor['character'] ?? '',
                    'foto' => $actor['profile_path']
                        ? $this->imageBaseUrl . 'w185' . $actor['profile_path']
                        : '/images/no-profile.jpg'
                ];
            }, array_slice($data['credits']['cast'], 0, 12));
        } else {
            $serie->reparto = [];
        }

        // Procesar equipo si existe
        if (isset($data['credits']) && isset($data['credits']['crew'])) {
            $serie->equipo = array_map(function ($crew) {
                return [
                    'nombre' => $crew['name'],
                    'trabajo' => $crew['job'] ?? '',
                    'departamento' => $crew['department'] ?? '',
                    'foto' => $crew['profile_path']
                        ? $this->imageBaseUrl . 'w185' . $crew['profile_path']
                        : '/images/no-profile.jpg'
                ];
            }, array_slice($data['credits']['crew'], 0, 8));
        } else {
            $serie->equipo = [];
        }

        // Procesar series similares
        if (isset($data['similar']) && isset($data['similar']['results'])) {
            $serie->series_similares = array_map(function ($similar) {
                return [
                    'id' => $similar['id'],
                    'titulo' => $similar['name'],
                    'poster' => $similar['poster_path']
                        ? $this->imageBaseUrl . 'w300' . $similar['poster_path']
                        : '/images/no-poster.jpg',
                    'fecha' => isset($similar['first_air_date'])
                        ? date('Y', strtotime($similar['first_air_date']))
                        : 'Desconocido',
                    'puntuacion' => $similar['vote_average'] * 10
                ];
            }, array_slice($data['similar']['results'], 0, 6));
        } else {
            $serie->series_similares = [];
        }

        // Procesar series recomendadas
        if (isset($data['recommendations']) && isset($data['recommendations']['results'])) {
            $serie->recomendaciones = array_map(function ($rec) {
                return [
                    'id' => $rec['id'],
                    'titulo' => $rec['name'],
                    'poster' => $rec['poster_path']
                        ? $this->imageBaseUrl . 'w300' . $rec['poster_path']
                        : '/images/no-poster.jpg',
                    'fecha' => isset($rec['first_air_date'])
                        ? date('Y', strtotime($rec['first_air_date']))
                        : 'Desconocido',
                    'puntuacion' => $rec['vote_average'] * 10
                ];
            }, array_slice($data['recommendations']['results'], 0, 6));
        } else {
            $serie->recomendaciones = [];
        }

        // Otras propiedades adicionales
        $serie->redes_sociales = [];

        return $serie;
    }

    /**
     * Obtiene el trailer de una serie para la API.
     */
    public function getTrailer($id)
    {
        try {
            // Intentar obtener datos desde la API de TMDB
            $response = Http::get("https://api.themoviedb.org/3/tv/{$id}/videos", [
                'api_key' => $this->apiKey,
                'language' => 'es-ES'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $trailers = collect($data['results'])->filter(function ($video) {
                    return $video['site'] === 'YouTube' &&
                           in_array($video['type'], ['Trailer', 'Teaser']) &&
                           $video['official'] == true;
                });

                // Si no hay trailer en español, intentamos con inglés
                if ($trailers->isEmpty()) {
                    $responseEn = Http::get("https://api.themoviedb.org/3/tv/{$id}/videos", [
                        'api_key' => $this->apiKey,
                        'language' => 'en-US'
                    ]);

                    if ($responseEn->successful()) {
                        $dataEn = $responseEn->json();
                        $trailers = collect($dataEn['results'])->filter(function ($video) {
                            return $video['site'] === 'YouTube' &&
                                   in_array($video['type'], ['Trailer', 'Teaser']) &&
                                   $video['official'] == true;
                        });
                    }
                }

                if ($trailers->isNotEmpty()) {
                    // Preferimos trailers oficiales
                    $trailer = $trailers->first();
                    return response()->json([
                        'success' => true,
                        'key' => $trailer['key'],
                        'name' => $trailer['name']
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ningún trailer para esta serie.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo encontrar la serie solicitada.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al cargar los datos: ' . $e->getMessage()
            ]);
        }
    }
}

