@extends('layout')

@section('content')
    <h1>Editar amistad</h1>

    <form action="{{ route('amigos.update', $amigo) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Usuario A</label>
        <select name="user_id">
            <option value="">-- Selecciona usuario --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(old('user_id', $amigo->user_id) == $user->id)>{{ $user->name }} ({{ $user->id }})</option>
            @endforeach
        </select>
        @error('user_id')<div class="error">{{ $message }}</div>@enderror

        <br/><br/>

        <label>Usuario B</label>
        <select name="friend_id">
            <option value="">-- Selecciona usuario --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(old('friend_id', $amigo->friend_id) == $user->id)>{{ $user->name }} ({{ $user->id }})</option>
            @endforeach
        </select>
        @error('friend_id')<div class="error">{{ $message }}</div>@enderror

        <br/><br/>

        <button type="submit">Actualizar</button>
    </form>

    <a href="{{ route('amigos.index') }}">Volver</a>
@endsection