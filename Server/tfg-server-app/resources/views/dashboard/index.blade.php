@extends('layout')

@section('content')
    <h1>Dashboard</h1>

    <h2>Juegos</h2>
    @include('juegos._list')
    
    <h2>Logros</h2>
    @include('logros._list')

    <h2>Resultados</h2>
    @include('resultados._list')

    <h2>Puntuaciones</h2>
    @include('puntuaciones._list')

    <h2>Amigos</h2>
    @include('amigos._list')

    @if (!empty($users))
        <h2>Usuarios</h2>
        @include('users._list')
    @endif
@endsection