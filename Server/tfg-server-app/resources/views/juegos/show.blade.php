@extends('layout')

@section('content')
    <h1>Ver Juego</h1>

    <label>Nombre</label>
    <input type="text" name="name" value="{{ $juego->name }}" disabled/>

    <br/><br/>

    <label>Descripcion</label>
    <input type="text" name="description" value="{{ $juego->description }}" disabled/>

    <br/><br/>

    <a href="{{ route('juegos.index') }}">Volver</a>
@endsection