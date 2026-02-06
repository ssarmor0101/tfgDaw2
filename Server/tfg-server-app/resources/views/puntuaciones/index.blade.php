@extends('layout')

@section('content')
    <h1>Puntuaciones</h1>
    <a href="{{ route('puntuaciones.create') }}">Crear puntuacion</a>
    
    @include('puntuaciones._list')
@endsection