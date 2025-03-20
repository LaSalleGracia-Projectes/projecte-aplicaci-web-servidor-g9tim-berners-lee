@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('profile.css') }}">
@endpush
@section('content')
<div class="profile-container">
    <div class="profile-form-container">
        <h1>Editar Perfil</h1>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input">
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="biografia">Biografía</label>
                <textarea id="biografia" name="biografia" rows="5" class="form-textarea">{{ old('biografia', $user->biografia) }}</textarea>
                @error('biografia')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group profile-photo-upload">
                <label>Foto de Perfil</label>
                <div class="current-photo">
                    @if($user->foto_perfil)
                        <img src="{{ asset('storage/' . $user->foto_perfil) }}" alt="Foto de perfil actual">
                    @else
                        <div class="photo-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>

                <div class="file-upload">
                    <label for="foto_perfil" class="file-upload-label">
                        <i class="fas fa-upload"></i> Seleccionar nueva foto
                    </label>
                    <input type="file" id="foto_perfil" name="foto_perfil" class="file-input" accept="image/*">
                    <span class="selected-file-name">Ningún archivo seleccionado</span>
                </div>

                @error('foto_perfil')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-neon">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="{{ route('profile.show') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('profile.js') }}"></script>
@endsection
