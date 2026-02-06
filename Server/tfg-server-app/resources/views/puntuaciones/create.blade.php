@extends('layout')

@section('content')
    <h1>Crear Puntuacion</h1>
    <form action="{{ route('puntuaciones.store') }}" method="POST">
        @csrf

        <label>Usuario</label>
        <select name="user_id">
            <option value="">-- Selecciona una opcion --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>

        @error('user_id')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Juego</label>
        <select name="juego_id">
            <option value="">-- Selecciona una opcion --</option>
            @foreach ($juegos as $juego)
                <option value="{{ $juego->id }}">{{ $juego->name }}</option>
            @endforeach
        </select>

        @error('juego_id')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Puntuacion</label>
        <input type="number" name="puntuacion"/>

        @error('puntuacion')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <button type="submit">Enviar</button>

        <br/><br/>

    </form>

    <a href="{{ route('puntuaciones.index') }}">Volver</a>
@endsection