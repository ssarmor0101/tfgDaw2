@extends('layout')

@section('content')
    <h1>Puntuaciones</h1>
    @if(isset($extraData['createButton']) && $extraData['createButton'])
        <a href="{{ route('puntuaciones.create') }}">Crear puntuacion</a>
    @endif
    
    @include('puntuaciones._list')

    @if(!empty($puntuaciones))
        <div class="pagination">
            {{ $puntuaciones->links() }}
        </div>
    @endif
@endsection