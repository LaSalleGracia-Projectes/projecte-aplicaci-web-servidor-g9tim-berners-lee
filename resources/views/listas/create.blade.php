@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/listas.css') }}">
@endpush

@section('content')
<div class="create-list-container">
    <div class="create-list-header">
        <div class="header-content">
            <h1><i class="fas fa-list-ul"></i> Crear Nueva Lista</h1>
            <p class="subtitle">Crea una lista personalizada para organizar tus películas favoritas</p>
        </div>
        <a href="{{ route('profile.show', request('user_id')) }}" class="btn-neon">
            <i class="fas fa-arrow-left"></i> Volver al Perfil
        </a>
    </div>

    <div class="create-list-form-container">
        <form action="{{ route('listas.store') }}" method="POST" class="create-list-form">
            @csrf
            <input type="hidden" name="user_id" value="{{ request('user_id') }}">

            <div class="form-section">
                <div class="form-group">
                    <label for="nombre_lista">
                        <i class="fas fa-heading"></i>
                        Nombre de la Lista
                    </label>
                    <div class="input-wrapper">
                        <input type="text"
                               id="nombre_lista"
                               name="nombre_lista"
                               class="form-control @error('nombre_lista') is-invalid @enderror"
                               placeholder="Ej: Películas de Ciencia Ficción"
                               required>
                        @error('nombre_lista')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">
                        <i class="fas fa-align-left"></i>
                        Descripción
                    </label>
                    <div class="input-wrapper">
                        <textarea id="descripcion"
                                name="descripcion"
                                class="form-control @error('descripcion') is-invalid @enderror"
                                rows="4"
                                placeholder="Describe tu lista (opcional)"></textarea>
                        @error('descripcion')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <div class="description-tips">
                            <p>Sugerencias:</p>
                            <ul>
                                <li>Describe el tema o género de las películas</li>
                                <li>Menciona qué hace única a esta lista</li>
                                <li>Añade hashtags relevantes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" onclick="window.history.back();" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-neon btn-create">
                    <i class="fas fa-save"></i> Crear Lista
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/listas.js') }}"></script>
@endpush
