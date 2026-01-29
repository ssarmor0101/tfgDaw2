@extends('layout')

@section('content')
    <h1>Ver Usuario</h1>

    <label>Nombre</label>
    <input type="text" name="name" value="{{ $user->name }}" disabled/>

    @error('name')
        <div class="error">{{ $message }}</div>
    @enderror

    <br/><br/>

    <label>Email</label>
    <input type="email" name="email" value="{{ $user->email }}" disabled/>

    @error('email')
        <div class="error">{{ $message }}</div>
    @enderror

    <br/><br/>

    <label>Rol</label>
    <select name="rol_id" disabled>
        @foreach ($roles as $rol)
            <option value="{{ $rol->id }}" @selected($user->rol->id == $rol->id)>{{ $rol->name }}</option>
        @endforeach
    </select>

    <br/><br/>

    <a href="{{ route('users.index') }}">Volver</a>
@endsection