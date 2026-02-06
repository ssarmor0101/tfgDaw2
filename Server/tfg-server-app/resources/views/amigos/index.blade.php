@extends('layout')

@section('content')
    <h1>Amigos</h1>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('amigos.create') }}">Crear amistad</a>

    @include('amigos._list')
@endsection