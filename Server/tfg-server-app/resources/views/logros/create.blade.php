@extends('layout')

@section('content')
    <h1>Crear Logro</h1>
    <form action="{{ route('logros.store') }}" method="POST">
        @csrf

        <label>Juego</label>
        <select name="juego_id">
            <option value="">-- Selecciona una opcion --</option>
            @foreach ($juegos as $juego)
                <option value="{{ $juego->id }}">{{ $juego->name }}</option>
            @endforeach
        </select>

        <br/><br/>

        <label>Nombre</label>
        <input type="text" name="name" value="{{ old('name') }}"/>

        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Descripcion</label>
        <input type="text" name="description" value="{{ old('description') }}"/>

        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <button type="submit">Enviar</button>

        <br/><br/>

    </form>

    <a href="{{ route('logros.index') }}">Volver</a>
@endsection