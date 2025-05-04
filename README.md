# CritFlix - Plataforma de críticas de películas y series

![CritFlix Logo](https://via.placeholder.com/200x100?text=CritFlix)

## 📝 Descripción

CritFlix es una innovadora plataforma que permite a los usuarios explorar, valorar y compartir sus opiniones sobre películas y series. La aplicación conecta amantes del cine y las series de televisión, facilitando la creación de comunidades en torno a sus contenidos favoritos.

## ✨ Características principales

- **Exploración de contenido**: Navega por un extenso catálogo de películas y series desde la API de TMDB
- **Sistema de valoraciones**: Califica películas y series con un sistema intuitivo
- **Comentarios y discusiones**: Participa en conversaciones sobre tus contenidos favoritos
- **Listas personalizadas**: Crea y comparte listas temáticas (favoritos, pendientes, recomendados, etc.)
- **Perfiles de usuario**: Personaliza tu perfil y muestra tus preferencias cinematográficas
- **Notificaciones**: Mantente al día con las interacciones y novedades
- **Roles especiales**: Usuarios críticos verificados con opiniones destacadas
- **Recomendaciones personalizadas**: Descubre nuevo contenido basado en tus gustos

## 🛠️ Tecnologías utilizadas

### Backend
- **Laravel**: Framework PHP para el desarrollo del servidor
- **MySQL**: Base de datos relacional
- **Sanctum**: Sistema de autenticación mediante tokens
- **Eloquent ORM**: Para la gestión de la base de datos

### Frontend
- **React/Vue.js**: Framework JavaScript para la interfaz de usuario
- **Tailwind CSS**: Framework CSS para el diseño
- **Axios**: Cliente HTTP para comunicación con la API
- **TMDB API**: Fuente de datos para películas y series

## 📊 Estructura de la base de datos

### Tablas principales
- `users`: Información de usuarios registrados
- `comentarios`: Comentarios de usuarios sobre películas y series
- `valoraciones`: Puntuaciones asignadas a películas y series
- `listas`: Colecciones personalizadas creadas por usuarios
- `contenidos_listas`: Relación entre listas y contenidos
- `likes_comentarios`: Interacciones con comentarios
- `respuestas_comentarios`: Respuestas a comentarios existentes
- `notificaciones`: Sistema de alertas para usuarios
- `solicitudes_critico`: Gestión de peticiones para convertirse en crítico

## 🔄 API Endpoints

### Autenticación
- `POST /api/register`: Registro de nuevos usuarios
- `POST /api/login`: Inicio de sesión
- `POST /api/logout`: Cierre de sesión (requiere autenticación)

### Usuarios
- `GET /api/usuarios`: Listar usuarios
- `GET /api/usuarios/{id}`: Obtener información de un usuario
- `PUT /api/usuarios/{id}`: Actualizar información de usuario
- `DELETE /api/usuarios/{id}`: Eliminar usuario

### Películas y Series
- `GET /api/peliculas_series`: Listar contenido almacenado
- `GET /api/peliculas_series/{id}`: Obtener detalles de un contenido

### Valoraciones
- `GET /api/valoraciones`: Listar valoraciones
- `POST /api/valoraciones`: Crear valoración
- `GET /api/valoraciones/usuario/{userId}`: Obtener favoritos de un usuario
- `GET /api/valoraciones/check/{userId}/{tmdb_id}`: Verificar estado de favorito

### Comentarios
- `GET /api/comentarios`: Listar todos los comentarios
- `POST /api/comentarios`: Crear un comentario
- `GET /api/comentarios/{id}`: Obtener un comentario específico
- `PUT /api/comentarios/{id}`: Actualizar un comentario
- `DELETE /api/comentarios/{id}`: Eliminar un comentario
- `GET /api/comentarios/tmdb/{tmdbId}/{tipo}`: Obtener comentarios por ID de TMDB
- `GET /api/comentarios-pelicula/{tmdbId}`: Obtener comentarios de una película
- `GET /api/comentarios-serie/{tmdbId}`: Obtener comentarios de una serie

### Respuestas a comentarios
- `GET /api/respuestas_comentarios`: Listar todas las respuestas
- `POST /api/respuestas-comentarios`: Crear una respuesta
- `GET /api/respuestas_comentarios/comentario/{comentarioId}`: Obtener respuestas de un comentario

### Likes/Dislikes
- `GET /api/likes_comentarios/status/{comentarioId}/{userId}`: Verificar estado de like
- `GET /api/likes_comentarios/count/{comentarioId}`: Contar likes de un comentario
- `POST /api/likes_comentarios`: Crear/actualizar like o dislike

### Listas personalizadas
- `GET /api/listas`: Listar todas las listas
- `POST /api/listas`: Crear una lista
- `GET /api/listas/{id}`: Obtener una lista específica
- `PUT /api/listas/{id}`: Actualizar una lista
- `DELETE /api/listas/{id}`: Eliminar una lista
- `GET /api/listas/user/{userId}`: Obtener listas de un usuario

### Contenido de listas
- `GET /api/contenido_listas`: Listar todo el contenido de listas
- `POST /api/contenido_listas`: Agregar contenido a una lista
- `DELETE /api/contenido_listas/{id}`: Eliminar contenido de una lista
- `GET /api/contenido_listas/lista/{id_lista}`: Obtener contenido de una lista específica

### Notificaciones
- `GET /api/notificaciones/user/{userId}`: Obtener notificaciones de un usuario
- `PUT /api/notificaciones/read/{id}`: Marcar notificación como leída
- `PUT /api/notificaciones/read_all/{userId}`: Marcar todas las notificaciones como leídas

### Solicitudes de crítico
- `GET /api/solicitudes_critico`: Listar solicitudes de crítico
- `POST /api/solicitudes_critico`: Crear solicitud de crítico
- `GET /api/solicitudes_critico/user/{userId}`: Obtener solicitudes de un usuario

## 🚀 Instalación y configuración

### Requisitos previos
- PHP 8.1 o superior
- Composer
- Node.js y npm
- MySQL

### Pasos para la instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/critflix.git
   cd critflix
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurar la base de datos en el archivo .env**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=critflix
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Ejecutar migraciones**
   ```bash
   php artisan migrate
   ```

6. **Instalar dependencias de JavaScript**
   ```bash
   npm install
   ```

7. **Compilar assets**
   ```bash
   npm run dev
   ```

8. **Iniciar el servidor de desarrollo**
   ```bash
   php artisan serve
   ```

## 👥 Equipo de desarrollo

- **Backend**: Grupo TIM BERNERS-LEE
- **Frontend**: Grupo TIM BERNERS-LEE
- **Diseño UX/UI**: Grupo TIM BERNERS-LEE
- **Base de datos**: Grupo TIM BERNERS-LEE

## 📄 Licencia

Este proyecto está licenciado bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

## 🔮 Futuras mejoras

- Implementación de un sistema de recomendaciones basado en machine learning
- Integración con redes sociales
- Sistema de logros y gamificación
- Transmisión y eventos en vivo para estrenos importantes
- Marketplace para contenido exclusivo de críticos

---

© 2025 CritFlix - Desarrollado por Grupo TIM BERNERS-LEE
