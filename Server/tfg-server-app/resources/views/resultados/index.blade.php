@extends('layout')

@section('content')
    <h1>Logros</h1>
    <a href="{{ route('resultados.create') }}">Crear logro</a>
    
    @include('resultados._list')
@endsection