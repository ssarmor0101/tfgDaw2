@extends('layout')

@section('content')
    <h1>Ver Puntuacion</h1>

    <label>Usuario</label>
    <select name="user_id" disabled>
        <option value="">-- Selecciona una opcion --</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected($puntuacion->user->id == $user->id)>{{ $user->name }}</option>
        @endforeach
    </select>

    <br/><br/>

    <label>Juego</label>
    <select name="juego_id" disabled>
        <option value="">-- Selecciona una opcion --</option>
        @foreach ($juegos as $juego)
            <option value="{{ $juego->id }}" @selected($puntuacion->juego->id == $juego->id)>{{ $juego->name }}</option>
        @endforeach
    </select>

    <br/><br/>

    <label>Puntuacion</label>
    <input type="number" name="puntuacion" value="{{ $puntuacion->puntuacion }}" disabled/>

    <br/><br/>

    <a href="{{ route('puntuaciones.index') }}">Volver</a>
@endsection