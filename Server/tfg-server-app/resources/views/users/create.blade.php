@extends('layout')

@section('content')
    <h1>Crear Usuario</h1>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <label>Nombre</label>
        <input type="text" name="name" value="{{ old('name') }}"/>

        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}"/>

        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Rol</label>
        <select name="rol_id">
            <option value="">-- Selecciona una opcion --</option>
            @foreach ($roles as $rol)
                <option value="{{ $rol->id }}">{{ $rol->name }}</option>
            @endforeach
        </select>

        <button type="submit">Enviar</button>

        <br/><br/>

    </form>

    <a href="{{ route('users.index') }}">Volver</a>
@endsection