@extends('layout')

@section('content')
    <h1>Ver Resultado</h1>

    <label>Usuario</label>
    <select name="user_id" disabled>
        <option value="">-- Selecciona una opcion --</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected($resultado->user == $user)>{{ $user->name }}</option>
        @endforeach
    </select>

    <br/><br/>

    <label>Logro</label>
    <select name="logro_id" disabled>
        <option value="">-- Selecciona una opcion --</option>
        @foreach ($logros as $logro)
            <option value="{{ $logro->id }}" @selected($resultado->logro == $logro)>{{ $logro->name }}</option>
        @endforeach
    </select>

    <br/><br/>

    <a href="{{ route('resultados.index') }}">Volver</a>
@endsection