@extends('layout')

@section('content')
    <h1>Ver amistad</h1>

    <label>Usuario A</label>
    <select name="user_id" disabled>
        <option value="">-- Selecciona usuario --</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected($amigo->user_id == $user->id)>{{ $user->name }} ({{ $user->id }})</option>
        @endforeach
    </select>
    @error('user_id')<div class="error">{{ $message }}</div>@enderror

    <br/><br/>

    <label>Usuario B</label>
    <select name="friend_id" disabled>
        <option value="">-- Selecciona usuario --</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected($amigo->friend_id == $user->id)>{{ $user->name }} ({{ $user->id }})</option>
        @endforeach
    </select>
    @error('friend_id')<div class="error">{{ $message }}</div>@enderror

    <br/><br/>

    <a href="{{ route('amigos.index') }}">Volver</a>
@endsection