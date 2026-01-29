@extends('layout')

@section('content')
    <h1>Logros</h1>
    <a href="{{ route('logros.create') }}">Crear logro</a>
    
    @include('logros._list')
@endsection