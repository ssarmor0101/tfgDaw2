@extends('layout')

@section('content')
    <h1>Crear Juego</h1>
    <form action="{{ route('juegos.store') }}" method="POST">
        @csrf

        <label>Nombre</label>
        <input type="text" name="name" value="{{ old('name') }}"/>

        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Descripcion</label>
        <input type="text" name="description" value="{{ old('description') }}"/>

        @error('description')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <button type="submit">Enviar</button>

        <br/><br/>

    </form>

    <a href="{{ route('juegos.index') }}">Volver</a>
@endsection