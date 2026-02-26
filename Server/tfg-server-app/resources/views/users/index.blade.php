@extends('layout')

@section('content')
    <h1>Usuarios</h1>
    <a href="{{ route('users.create') }}">Crear usuario</a>
    
    @include('users._list')

    @if(!empty($users))
        <div class="pagination">
            {{ $users->links() }}
        </div>
    @endif
@endsection