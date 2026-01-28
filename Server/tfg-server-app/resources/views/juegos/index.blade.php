@extends('layout')

@section('content')
    <h1>Juegos</h1>
    <a href="{{ route('juegos.create') }}">Crear tarea</a>
    
    @include('juegos._list')
@endsection