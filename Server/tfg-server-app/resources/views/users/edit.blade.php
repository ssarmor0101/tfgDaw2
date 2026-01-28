@extends('layout')

@section('content')
    <h1>Editar Usuario</h1>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre</label>
        <input type="text" name="name" value="{{ $user->name }}"/>

        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Descripcion</label>
        <input type="text" name="description" value="{{ $user->description }}"/>

        @error('description')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <button type="submit">Enviar</button>

        <br/><br/>

    </form>

    <a href="{{ route('users.index') }}">Volver</a>
@endsection