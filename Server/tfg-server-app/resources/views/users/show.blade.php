@extends('layout')

@section('content')
    <h1>Ver Juego</h1>

    <label>Nombre</label>
    <input type="text" name="name" value="{{ $user->name }}" disabled/>

    <br/><br/>

    <label>Descripcion</label>
    <input type="text" name="description" value="{{ $user->description }}" disabled/>

    <br/><br/>

    <a href="{{ route('users.index') }}">Volver</a>
@endsection