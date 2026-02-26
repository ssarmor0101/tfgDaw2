@extends('layout')

@section('content')
    <h1>Juegos</h1>
    @if(isset($extraData['createButton']) && $extraData['createButton'])
        <a href="{{ route('juegos.create') }}">Crear tarea</a>
    @endif
    
    @include('juegos._list')

    @if(!empty($juegos))
        <div class="pagination">
            {{ $juegos->links() }}
        </div>
    @endif
@endsection