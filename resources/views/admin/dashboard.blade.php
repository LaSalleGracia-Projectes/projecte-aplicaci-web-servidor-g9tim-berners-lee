@extends('layouts.admin')

@section('title', 'Dashboard - CritFlix Admin')

@section('header-title', 'Panel de Control')

@push('styles')
<style>
    /* Estilos para las tarjetas de estadísticas */
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        transition: all var(--transition-speed) ease;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-color: var(--verde-neon);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent, rgba(0, 255, 0, 0.1));
        opacity: 0;
        transition: opacity var(--transition-speed) ease;
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    /* Estilos para gráfico de distribución de roles */
    .roles-chart-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        margin-bottom: 2rem;
        transition: all var(--transition-speed) ease;
    }

    .roles-chart-card:hover {
        box-shadow: var(--shadow-lg);
        border-color: var(--verde-neon);
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        font-size: 1.2rem;
        margin: 0;
        font-weight: 600;
        color: var(--text-light);
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Estilos para tablas recientes */
    .recent-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 1rem;
    }

    .recent-table th {
        padding: 1rem;
        text-align: left;
        background: rgba(0, 255, 0, 0.1);
        color: var(--verde-neon);
        font-weight: 500;
        font-size: 0.9rem;
        border-bottom: 1px solid var(--border-color);
    }

    .recent-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .recent-table tr:hover td {
        background: rgba(0, 255, 0, 0.05);
    }

    .recent-table .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .recent-table .role-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .role-admin {
        background-color: rgba(255, 48, 96, 0.15);
        color: #ff3060;
        border: 1px solid rgba(255, 48, 96, 0.3);
    }

    .role-premium {
        background-color: rgba(255, 204, 0, 0.15);
        color: #ffcc00;
        border: 1px solid rgba(255, 204, 0, 0.3);
    }

    .role-critico {
        background-color: rgba(0, 232, 255, 0.15);
        color: #00e8ff;
        border: 1px solid rgba(0, 232, 255, 0.3);
    }

    .role-usuario {
        background-color: rgba(0, 255, 102, 0.15);
        color: #00ff66;
        border: 1px solid rgba(0, 255, 102, 0.3);
    }

    .rating-stars {
        color: #ffcc00;
    }

    .view-all {
        display: inline-block;
        margin-top: 1rem;
        font-size: 0.9rem;
        color: var(--verde-neon);
        text-decoration: none;
        transition: all var(--transition-speed) ease;
    }

    .view-all:hover {
        text-decoration: underline;
        color: var(--verde-neon);
        text-shadow: 0 0 5px var(--verde-neon);
    }

    /* Filtros para las tablas */
    .table-filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        color: var(--text-muted);
        cursor: pointer;
        transition: all var(--transition-speed) ease;
    }

    .filter-btn.active {
        border-color: var(--verde-neon);
        color: var(--verde-neon);
        background: rgba(0, 255, 0, 0.1);
    }

    .filter-btn:hover {
        border-color: var(--verde-neon);
        color: var(--verde-neon);
    }

    /* Estilos para la leyenda del gráfico */
    .legend-color.admin {
        background-color: #ff3060;
        box-shadow: 0 0 5px rgba(255, 48, 96, 0.7);
    }

    .legend-color.premium {
        background-color: #ffcc00;
        box-shadow: 0 0 5px rgba(255, 204, 0, 0.7);
    }

    .legend-color.critico {
        background-color: #00e8ff;
        box-shadow: 0 0 5px rgba(0, 232, 255, 0.7);
    }

    .legend-color.usuario {
        background-color: #00ff66;
        box-shadow: 0 0 5px rgba(0, 255, 102, 0.7);
    }

    .legend-value.admin {
        color: #ff3060;
    }

    .legend-value.premium {
        color: #ffcc00;
    }

    .legend-value.critico {
        color: #00e8ff;
    }

    .legend-value.usuario {
        color: #00ff66;
    }

    /* Ajustes para que todo se vea bien cuando hay barra lateral */
    .admin-content {
        padding: 1.5rem;
        width: 100%;
    }

    .roles-chart-container {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(300px, 400px);
        gap: 2rem;
    }

    @media (max-width: 1199px) {
        .roles-chart-container {
            grid-template-columns: 1fr;
        }

        .roles-legend {
            border-left: none;
            border-top: 1px solid var(--border-color);
            padding-left: 0;
            padding-top: 1.5rem;
            margin-top: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="admin-content">
    <!-- Tarjetas de estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>Usuarios Totales</h3>
                <div class="stat-number">{{ $stats['total_users'] }}</div>
                <div class="stat-detail">
                    <i class="fas fa-arrow-up"></i> {{ $stats['new_users_today'] }} nuevos hoy
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-film"></i>
            </div>
            <div class="stat-info">
                <h3>Películas/Series</h3>
                <div class="stat-number">{{ $stats['total_movies'] }}</div>
                <div class="stat-detail">
                    <i class="fas fa-database"></i> En la base de datos
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-info">
                <h3>Valoraciones</h3>
                <div class="stat-number">{{ $stats['total_reviews'] }}</div>
                <div class="stat-detail">
                    <i class="fas fa-comments"></i> Con comentarios
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-info">
                <h3>Visitas</h3>
                <div class="stat-number">{{ $stats['visits_today'] ?? 0 }}</div>
                <div class="stat-detail">
                    <i class="fas fa-calendar"></i> En el día de hoy
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución de roles de usuario -->
    <div class="roles-chart-card">
        <div class="card-header">
            <h3>Distribución de Roles de Usuario</h3>
            <div class="card-actions">
                <button class="btn-neon" onclick="window.critflixAdmin.reloadStats()">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="roles-chart-container">
                <div class="roles-chart">
                    <canvas id="rolesChart" width="400" height="400"></canvas>
                </div>
                <div class="roles-legend">
                    <div class="legend-item">
                        <div class="legend-color admin"></div>
                        <div class="legend-info">
                            <div class="legend-label">Administradores</div>
                            <div class="legend-value admin">{{ $stats['role_percentages']['admin']['count'] ?? 0 }} ({{ $stats['role_percentages']['admin']['percentage'] ?? 0 }}%)</div>
                        </div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color premium"></div>
                        <div class="legend-info">
                            <div class="legend-label">Premium</div>
                            <div class="legend-value premium">{{ $stats['role_percentages']['premium']['count'] ?? 0 }} ({{ $stats['role_percentages']['premium']['percentage'] ?? 0 }}%)</div>
                        </div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color critico"></div>
                        <div class="legend-info">
                            <div class="legend-label">Críticos</div>
                            <div class="legend-value critico">{{ $stats['role_percentages']['critico']['count'] ?? 0 }} ({{ $stats['role_percentages']['critico']['percentage'] ?? 0 }}%)</div>
                        </div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color usuario"></div>
                        <div class="legend-info">
                            <div class="legend-label">Usuarios</div>
                            <div class="legend-value usuario">{{ $stats['role_percentages']['usuario']['count'] ?? 0 }} ({{ $stats['role_percentages']['usuario']['percentage'] ?? 0 }}%)</div>
                        </div>
                    </div>
                    <div class="roles-total">
                        <strong>Total de usuarios:</strong> {{ $stats['total_users'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos usuarios registrados -->
    <div class="recent-data-section">
        <div class="roles-chart-card">
            <div class="card-header">
                <h3>Usuarios recientes</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.users') }}" class="btn-neon">
                        <i class="fas fa-eye"></i> Ver todos
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <img
                                            src="{{ $user->imagen_perfil ? asset('storage/' . $user->imagen_perfil) : asset('img/avatar-default.png') }}"
                                            alt="{{ $user->name }}"
                                            class="user-avatar"
                                            width="40"
                                            height="40"
                                        >
                                        <span>{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="role-badge role-{{ $user->rol }}">{{ ucfirst($user->rol) }}</span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay usuarios recientes</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('admin.users') }}" class="view-all">Ver todos los usuarios <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Últimas valoraciones -->
    <div class="recent-data-section">
        <div class="roles-chart-card">
            <div class="card-header">
                <h3>Valoraciones recientes</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.reviews') }}" class="btn-neon">
                        <i class="fas fa-eye"></i> Ver todas
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Película/Serie</th>
                                <th>Valoración</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReviews as $review)
                            <tr>
                                <td>{{ $review->user_name }}</td>
                                <td>{{ $review->movie_title }}</td>
                                <td>
                                    <div class="rating-stars">
                                        @for($i = 0; $i < $review->valoracion; $i++)
                                            <i class="fas fa-star"></i>
                                        @endfor
                                        @for($i = $review->valoracion; $i < 5; $i++)
                                            <i class="far fa-star"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay valoraciones recientes</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('admin.reviews') }}" class="view-all">Ver todas las valoraciones <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colores para el gráfico
    const colorRojo = '#ff3060';     // Rojo para admin
    const colorAmarillo = '#ffcc00'; // Amarillo para premium
    const colorCyan = '#00e8ff';     // Cyan para crítico
    const colorVerde = '#00ff66';    // Verde para usuario

    // Datos para el gráfico circular
    const roleData = {
        labels: ['Administradores', 'Premium', 'Críticos', 'Usuarios'],
        datasets: [{
            data: [
                {{ $stats['role_percentages']['admin']['count'] ?? 0 }},
                {{ $stats['role_percentages']['premium']['count'] ?? 0 }},
                {{ $stats['role_percentages']['critico']['count'] ?? 0 }},
                {{ $stats['role_percentages']['usuario']['count'] ?? 0 }}
            ],
            backgroundColor: [
                colorRojo,
                colorAmarillo,
                colorCyan,
                colorVerde
            ],
            borderColor: '#111',
            borderWidth: 2,
            hoverBackgroundColor: [
                colorRojo,
                colorAmarillo,
                colorCyan,
                colorVerde
            ],
            hoverBorderColor: '#fff'
        }]
    };

    // Configuración del gráfico
    const chartConfig = {
        type: 'doughnut',
        data: roleData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 20, 0, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(0, 255, 0, 0.3)',
                    borderWidth: 1,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '70%',
            rotation: -90,
            circumference: 360,
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    };

    // Inicializar el gráfico
    const ctx = document.getElementById('rolesChart').getContext('2d');
    new Chart(ctx, chartConfig);
});
</script>
@endpush
