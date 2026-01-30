@extends('layout')

@section('content')
    <h1>Dashboard</h1>

    <h2>Juegos</h2>
    @include('juegos._list')
    
    <h2>Logros</h2>
    @include('logros._list')

    <h2>Usuarios</h2>
    @include('users._list')
@endsection