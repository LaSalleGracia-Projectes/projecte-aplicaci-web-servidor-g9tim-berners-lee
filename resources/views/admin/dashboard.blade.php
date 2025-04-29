@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/main.css') }}">
<style>
    /* Variables actualizadas */
    :root {
        --primary-color: var(--verde-neon);
        --secondary-color: var(--verde-claro);
        --neon-primary-glow: 0 0 5px rgba(20, 255, 20, 0.3);
        --neon-secondary-glow: 0 0 5px rgba(0, 255, 221, 0.3);
        --bg-gradient: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
    }

    /* Mejoras generales */
    body {
        padding-top: 0;
        background-color: #0a0a0a;
    }

    .admin-container {
        display: flex;
        min-height: 100vh;
        background: var(--bg-gradient);
    }

    .admin-sidebar {
        width: 250px;
        background-color: rgba(10, 10, 10, 0.95);
        border-right: 1px solid rgba(20, 255, 20, 0.1);
        padding: 1.5rem 0;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 100;
        overflow-y: auto;
    }

    .admin-main {
        flex: 1;
        margin-left: 250px;
        padding-bottom: 2rem;
    }

    .admin-header {
        position: sticky;
        top: 0;
        padding: 1rem 2rem;
        backdrop-filter: blur(10px);
        background-color: rgba(26, 26, 26, 0.9);
        border-bottom: 1px solid rgba(20, 255, 20, 0.1);
        z-index: 90;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .admin-header-title h2 {
        margin: 0;
        color: var(--verde-neon);
        font-size: 1.6rem;
        text-shadow: 0 0 10px rgba(20, 255, 20, 0.4);
    }

    .admin-header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .admin-content {
        padding: 2rem;
    }

    .admin-logo {
        text-align: center;
        padding: 1rem;
        margin-bottom: 2rem;
    }

    .admin-logo h1 {
        font-size: 1.8rem;
        color: #fff;
        margin: 0;
    }

    .admin-logo span {
        color: var(--verde-neon);
        text-shadow: 0 0 8px rgba(20, 255, 20, 0.5);
    }

    .admin-nav {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding: 0 1rem;
    }

    .admin-nav-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.8rem 1rem;
        border-radius: 8px;
        color: #ddd;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .admin-nav-item:hover {
        background-color: rgba(20, 255, 20, 0.1);
        color: #fff;
    }

    .admin-nav-item.active {
        background-color: rgba(20, 255, 20, 0.15);
        color: var(--verde-neon);
        border-left: 3px solid var(--verde-neon);
    }

    .admin-nav-item i {
        font-size: 1.2rem;
        width: 24px;
        text-align: center;
    }

    .admin-nav-divider {
        height: 1px;
        background-color: rgba(255, 255, 255, 0.1);
        margin: 1rem 0;
    }

    .admin-search {
        position: relative;
        max-width: 300px;
    }

    .admin-search input {
        width: 100%;
        padding: 0.6rem 1rem 0.6rem 2.5rem;
        border-radius: 20px;
        border: 1px solid rgba(20, 255, 20, 0.2);
        background-color: rgba(0, 0, 0, 0.3);
        color: #fff;
        font-size: 0.9rem;
    }

    .admin-search button {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
    }

    .admin-notifications {
        position: relative;
    }

    .admin-notifications button {
        background: none;
        border: none;
        color: #fff;
        font-size: 1.2rem;
        cursor: pointer;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .admin-notifications button:hover {
        background-color: rgba(20, 255, 20, 0.1);
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: var(--verde-neon);
        color: #000;
        font-size: 0.7rem;
        font-weight: bold;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: rgba(26, 26, 26, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(20, 255, 20, 0.1);
        border-radius: 10px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        border-color: rgba(20, 255, 20, 0.2);
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(20, 255, 20, 0.1);
        border-radius: 10px;
        font-size: 1.8rem;
        color: var(--verde-neon);
    }

    .stat-info {
        flex: 1;
    }

    .stat-info h3 {
        font-size: 1rem;
        color: #aaa;
        margin: 0 0 0.5rem 0;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: bold;
        color: #fff;
        margin: 0 0 0.3rem 0;
    }

    .stat-detail {
        font-size: 0.8rem;
        color: var(--verde-neon);
        margin: 0;
    }

    .stat-detail i {
        margin-right: 0.3rem;
    }

    .roles-chart-card {
        background-color: rgba(26, 26, 26, 0.6);
        border: 1px solid rgba(20, 255, 20, 0.1);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .roles-chart-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        padding: 2rem;
    }

    @media (max-width: 992px) {
        .roles-chart-container {
            grid-template-columns: 1fr;
        }
    }

    .roles-chart {
        position: relative;
        height: 300px;
    }

    .users-table-card, .reviews-table-card {
        background-color: rgba(26, 26, 26, 0.6);
        border: 1px solid rgba(20, 255, 20, 0.1);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .admin-sidebar {
            width: 70px;
            padding: 1rem 0;
        }

        .admin-sidebar .admin-logo h1 {
            font-size: 1rem;
        }

        .admin-sidebar .admin-nav-item span {
            display: none;
        }

        .admin-sidebar .admin-nav-item {
            justify-content: center;
            padding: 0.8rem;
        }

        .admin-sidebar .admin-nav-item i {
            margin: 0;
        }

        .admin-main {
            margin-left: 70px;
        }
    }

    /* Para asegurar que las tarjetas de usuarios y reseñas tengan suficiente espacio */
    .table-responsive {
        overflow-x: auto;
    }

    .table {
        min-width: 600px;
    }

    /* Ajustes adicionales para dispositivos más pequeños */
    @media (max-width: 576px) {
        .admin-content {
            padding: 1rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-card {
            padding: 1rem;
        }

        .roles-chart-container {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="admin-container">
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="admin-logo">
            <h1>Crit<span>Flix</span></h1>
        </div>
        <div class="admin-nav">
            <a href="{{ url('/admin') }}" class="admin-nav-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ url('/admin/users') }}" class="admin-nav-item">
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
            </a>
            <a href="{{ url('/admin/movies') }}" class="admin-nav-item">
                <i class="fas fa-film"></i>
                <span>Películas</span>
            </a>
            <a href="{{ url('/admin/reviews') }}" class="admin-nav-item">
                <i class="fas fa-star"></i>
                <span>Reseñas</span>
            </a>
            <div class="admin-nav-divider"></div>
            <a href="{{ url('/profile') }}" class="admin-nav-item">
                <i class="fas fa-user-cog"></i>
                <span>Perfil</span>
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="admin-nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <div class="admin-main">
        <!-- Header -->
        <div class="admin-header">
            <div class="admin-header-title">
                <h2>Panel de Control</h2>
            </div>
            <div class="admin-header-actions">
                <div class="admin-search">
                    <input type="text" placeholder="Buscar...">
                    <button type="button"><i class="fas fa-search"></i></button>
                </div>
                <div class="admin-notifications">
                    <button type="button"><i class="fas fa-bell"></i></button>
                    <span class="notification-badge">3</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="admin-content">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Usuarios</h3>
                        <p class="stat-number">{{ $stats['total_users'] }}</p>
                        <p class="stat-detail">
                            <i class="fas fa-arrow-up"></i> {{ $stats['new_users_today'] }} hoy
                        </p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-film"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Películas</h3>
                        <p class="stat-number">{{ $stats['total_movies'] }}</p>
                        <p class="stat-detail">
                            <i class="fas fa-plus"></i> 12 este mes
                        </p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Reseñas</h3>
                        <p class="stat-number">{{ $stats['total_reviews'] }}</p>
                        <p class="stat-detail">
                            <i class="fas fa-arrow-up"></i> 25 esta semana
                        </p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Visitas Hoy</h3>
                        <p class="stat-number">{{ $stats['visits_today'] }}</p>
                        <p class="stat-detail">
                            <i class="fas fa-arrow-up"></i> 12% vs ayer
                        </p>
                    </div>
                </div>
            </div>

            <!-- Roles Chart -->
            <div class="roles-chart-card">
                <div class="card-header">
                    <h3>Distribución de Roles</h3>
                    <div class="card-actions">
                        <button class="btn-neon">Exportar</button>
                    </div>
                </div>
                <div class="roles-chart-container">
                    <div class="roles-chart">
                        <canvas id="role-distribution-chart"></canvas>
                    </div>
                    <div class="roles-legend">
                        @php
                            $rolePercentages = $stats['role_percentages'];
                            $adminData = $rolePercentages['admin'] ?? ['count' => 0, 'percentage' => 0];
                            $premiumData = $rolePercentages['premium'] ?? ['count' => 0, 'percentage' => 0];
                            $criticoData = $rolePercentages['critico'] ?? ['count' => 0, 'percentage' => 0];
                            $usuarioData = $rolePercentages['usuario'] ?? ['count' => 0, 'percentage' => 0];
                        @endphp

                        <div class="legend-item" data-role="admin">
                            <div class="legend-color admin"></div>
                            <div class="legend-label">Administradores</div>
                            <div class="legend-value admin">{{ $adminData['count'] }} ({{ $adminData['percentage'] }}%)</div>
                        </div>
                        <div class="legend-item" data-role="premium">
                            <div class="legend-color premium"></div>
                            <div class="legend-label">Premium</div>
                            <div class="legend-value premium">{{ $premiumData['count'] }} ({{ $premiumData['percentage'] }}%)</div>
                        </div>
                        <div class="legend-item" data-role="critico">
                            <div class="legend-color critico"></div>
                            <div class="legend-label">Críticos</div>
                            <div class="legend-value critico">{{ $criticoData['count'] }} ({{ $criticoData['percentage'] }}%)</div>
                        </div>
                        <div class="legend-item" data-role="usuario">
                            <div class="legend-color usuario"></div>
                            <div class="legend-label">Usuarios</div>
                            <div class="legend-value usuario">{{ $usuarioData['count'] }} ({{ $usuarioData['percentage'] }}%)</div>
                        </div>

                        <div class="roles-total">
                            Total de usuarios: <span>{{ $stats['total_users'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="users-table-card">
                <div class="card-header">
                    <h3>Usuarios Recientes</h3>
                    <div class="card-actions">
                        <select class="table-filter">
                            <option value="">Todos los roles</option>
                            <option value="admin">Administradores</option>
                            <option value="premium">Premium</option>
                            <option value="critico">Críticos</option>
                            <option value="usuario">Usuarios</option>
                        </select>
                        <button class="btn-neon">Ver Todos</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Registrado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge role-{{ strtolower($user->rol) }}">
                                        {{ ucfirst($user->rol) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ url('/admin/users/'.$user->id) }}" class="action-btn">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ url('/admin/users/'.$user->id.'/edit') }}" class="action-btn">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="action-btn delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="reviews-table-card">
                <div class="card-header">
                    <h3>Reseñas Recientes</h3>
                    <div class="card-actions">
                        <select class="table-filter">
                            <option value="">Todas las reseñas</option>
                            <option value="positiva">Positivas</option>
                            <option value="negativa">Negativas</option>
                            <option value="neutral">Neutrales</option>
                        </select>
                        <button class="btn-neon">Ver Todas</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Película</th>
                                <th>Usuario</th>
                                <th>Puntuación</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReviews as $review)
                            <tr>
                                <td>{{ $review->movie_title }}</td>
                                <td>{{ $review->user_name }}</td>
                                <td>{{ $review->valoracion }}/10</td>
                                <td>{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ url('/admin/reviews/'.$review->id) }}" class="action-btn">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ url('/admin/reviews/'.$review->id.'/edit') }}" class="action-btn">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="action-btn delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
    @csrf
</form>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración del gráfico de roles
        const ctx = document.getElementById('role-distribution-chart').getContext('2d');

        const data = {
            labels: ['Administradores', 'Premium', 'Críticos', 'Usuarios'],
            datasets: [{
                data: [
                    {{ $adminData['count'] }},
                    {{ $premiumData['count'] }},
                    {{ $criticoData['count'] }},
                    {{ $usuarioData['count'] }}
                ],
                backgroundColor: [
                    '#ff4444', // Rojo neón - Admin
                    '#ffd700', // Amarillo neón - Premium
                    '#00ffdd', // Cyan neón - Crítico
                    '#14ff14'  // Verde neón - Usuario
                ],
                borderColor: '#0a0a0a',
                borderWidth: 2,
                hoverOffset: 15
            }]
        };

        const config = {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        },
                        padding: 10,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#333',
                        borderWidth: 1,
                        displayColors: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        boxPadding: 3
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        };

        const rolesChart = new Chart(ctx, config);

        // Interactividad para los items de la leyenda
        const legendItems = document.querySelectorAll('.legend-item');

        legendItems.forEach((item, index) => {
            item.addEventListener('mouseenter', () => {
                // Destacar el segmento correspondiente
                const dataset = rolesChart.data.datasets[0];
                const dataClone = [...dataset.data];

                dataClone.forEach((v, i) => {
                    dataClone[i] = (i === index) ? v * 1.1 : v * 0.9;
                });

                dataset.hoverOffset = 20;
                rolesChart.update();
            });

            item.addEventListener('mouseleave', () => {
                // Restaurar el estado normal
                const dataset = rolesChart.data.datasets[0];
                dataset.data = [
                    {{ $adminData['count'] }},
                    {{ $premiumData['count'] }},
                    {{ $criticoData['count'] }},
                    {{ $usuarioData['count'] }}
                ];
                dataset.hoverOffset = 15;
                rolesChart.update();
            });
        });
    });
</script>
@endsection
