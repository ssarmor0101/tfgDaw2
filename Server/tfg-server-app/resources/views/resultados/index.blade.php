@extends('layout')

@section('content')
    <h1>Resultados</h1>
    @if(isset($extraData['createButton']) && $extraData['createButton'])
        <a href="{{ route('resultados.create') }}">Crear logro</a>
    @endif

    @include('resultados._list')

    @if(!empty($resultados))
        <div class="pagination">
            {{ $resultados->links() }}
        </div>
    @endif
@endsection