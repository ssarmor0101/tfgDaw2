@extends('layout')

@section('content')
    <h1>Amigos</h1>

    @if(isset($extraData['createButton']) && $extraData['createButton'])
        <a href="{{ route('amigos.create') }}">Crear amistad</a>
    @endif

    @include('amigos._list')

    @if(!empty($amigos))
        <div class="pagination">
            {{ $amigos->links() }}
        </div>
    @endif
@endsection