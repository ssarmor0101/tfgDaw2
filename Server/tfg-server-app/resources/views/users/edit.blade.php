@extends('layout')

@section('content')
    <h1>Crear Usuario</h1>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}"/>

        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}"/>

        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Actualizar contraseña</label>
        <input type="checkbox" name="use_password" value="1" @checked( old('use_password') == true )/>

        <br/><br/>

        <label>Password</label>
        <input type="password" name="password"/>

        <br/><br/>

        <label>Confirm Password</label>
        <input type="password" name="password_confirmation"/>

        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror
        
        <br/><br/>

        <label>Rol</label>
        <select name="rol_id">
            @foreach ($roles as $rol)
                <option value="{{ $rol->id }}" @selected(old('rol_id', $user->rol->id) == $rol->id)>{{ $rol->name }}</option>
            @endforeach
        </select>

        <br/><br/>

        <button type="submit">Enviar</button>

        <br/><br/>

    </form>

    <a href="{{ route('users.index') }}">Volver</a>
@endsection