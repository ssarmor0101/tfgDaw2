@extends('layout')

@section('content')
    <h1>Editar Logro</h1>
    <form action="{{ route('resultados.update', $resultado) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Usuario</label>
        <select name="user_id">
            <option value="">-- Selecciona una opcion --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected($resultado->user == $user)>{{ $user->name }}</option>
            @endforeach
        </select>

        @error('user_id')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Logro</label>
        <select name="logro_id">
            <option value="">-- Selecciona una opcion --</option>
            @foreach ($logros as $logro)
                <option value="{{ $logro->id }}" @selected($resultado->logro == $logro)>{{ $logro->name }}</option>
            @endforeach
        </select>

        @error('logro_id')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <button type="submit">Enviar</button>

        <br/><br/>

    </form>

    <a href="{{ route('resultados.index') }}">Volver</a>
@endsection