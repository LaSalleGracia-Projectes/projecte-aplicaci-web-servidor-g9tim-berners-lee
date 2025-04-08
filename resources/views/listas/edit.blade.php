@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1>Editar Lista</h1>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('listas.update', $lista->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nombre_lista" class="form-label">Nombre de la Lista</label>
                            <input type="text" class="form-control @error('nombre_lista') is-invalid @enderror"
                                id="nombre_lista" name="nombre_lista" value="{{ old('nombre_lista', $lista->nombre_lista) }}" required>
                            @error('nombre_lista')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn-neon">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('listas.show', $lista->id) }}" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
