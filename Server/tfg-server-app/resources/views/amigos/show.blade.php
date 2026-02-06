@extends('layout')

@section('content')
    <h1>Ver amistad</h1>

    <p><strong>ID:</strong> {{ $amigo->id }}</p>
    <p><strong>Usuario A:</strong> {{ $amigo->user->name ?? '—' }} ({{ $amigo->user_id }})</p>
    <p><strong>Usuario B:</strong> {{ $amigo->friend->name ?? '—' }} ({{ $amigo->friend_id }})</p>
    <p><strong>Creada:</strong> {{ $amigo->created_at }}</p>

    <a href="{{ route('amigos.edit', $amigo) }}">Editar</a>
    |
    <a href="{{ route('amigos.index') }}">Volver</a>
@endsection