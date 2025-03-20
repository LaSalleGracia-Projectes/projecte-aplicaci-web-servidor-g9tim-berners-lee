@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('profile.css') }}">
@endpush
@section('content')
<div class="profile-container">
    <div class="profile-form-container">
        <h1>Cambiar Contraseña</h1>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('profile.password.update') }}" method="POST" class="profile-form">
            @csrf

            <div class="form-group">
                <label for="current_password">Contraseña Actual</label>
                <input type="password" id="current_password" name="current_password" required class="form-input">
                @error('current_password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <input type="password" id="password" name="password" required class="form-input">
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="form-input">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-neon">
                    <i class="fas fa-key"></i> Actualizar Contraseña
                </button>
                <a href="{{ route('profile.show') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
