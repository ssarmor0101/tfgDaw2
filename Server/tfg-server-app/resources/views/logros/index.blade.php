@extends('layout')

@section('content')
    <h1>Logros</h1>
    @if(isset($extraData['createButton']) && $extraData['createButton'])
        <a href="{{ route('logros.create') }}">Crear logro</a>
    @endif
    
    @include('logros._list')

    @if(!empty($logros))
        <div class="pagination">
            {{ $logros->links() }}
        </div>
    @endif
@endsection