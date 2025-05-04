@extends('layouts.admin')

@section('title', 'Prueba de Envío de Correos - CritFlix Admin')

@section('header-title', 'Prueba de Envío de Correos')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-envelope"></i> Enviar correo de bienvenida de prueba</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.email.test') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email">Dirección de correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Ingresa un correo electrónico de prueba"
                                value="{{ old('email') }}" required>
                            <small class="form-text text-muted">
                                Se enviará un correo de bienvenida de prueba a esta dirección.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Enviar correo de prueba
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-cog"></i> Configuración actual</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Driver</td>
                                    <td><code>{{ $mailConfig['driver'] }}</code></td>
                                </tr>
                                <tr>
                                    <td>Host</td>
                                    <td><code>{{ $mailConfig['host'] }}</code></td>
                                </tr>
                                <tr>
                                    <td>Puerto</td>
                                    <td><code>{{ $mailConfig['port'] }}</code></td>
                                </tr>
                                <tr>
                                    <td>Usuario</td>
                                    <td><code>{{ $mailConfig['username'] }}</code></td>
                                </tr>
                                <tr>
                                    <td>Contraseña</td>
                                    <td><code>{{ $mailConfig['password'] }}</code></td>
                                </tr>
                                <tr>
                                    <td>Dirección remitente</td>
                                    <td><code>{{ $mailConfig['from_address'] }}</code></td>
                                </tr>
                                <tr>
                                    <td>Nombre remitente</td>
                                    <td><code>{{ $mailConfig['from_name'] }}</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <p class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Para modificar esta configuración, edita el archivo <code>.env</code> de la aplicación.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
