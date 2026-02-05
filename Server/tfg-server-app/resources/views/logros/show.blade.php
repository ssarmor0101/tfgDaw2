@extends('layout')

@section('content')
    <h1>Ver Logro</h1>

    <label>Juego</label>
    <select name="juego_id" disabled>
        <option value="">-- Selecciona una opcion --</option>
        @foreach ($juegos as $juego)
            <option value="{{ $juego->id }}" @selected($logro->juego == $juego)>{{ $juego->name }}</option>
        @endforeach
    </select>

    <br/><br/>

    <label>Nombre</label>
    <input type="text" name="name" value="{{ old('name', $logro->name) }}" disabled/>
    <br/><br/>

    <label>Descripcion</label>
    <input type="text" name="description" value="{{ old('description', $logro->description) }}" disabled/>

    <br/><br/>

    <a href="{{ route('logros.index') }}">Volver</a>
@endsection