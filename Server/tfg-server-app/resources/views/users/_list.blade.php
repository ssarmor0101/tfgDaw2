@if(!empty($users))
    <ul>
        @foreach($users as $user)
            <li>
                {{ $user->name }} -
                {{ $user->email }} -
                {{ $user->rol->name }} -
                <a href="{{ route('users.show', $user) }}">Ver</a> -
                <a href="{{ route('users.edit', $user) }}">Editar</a> -
                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button>Eliminar</button>
                </form>
            </li>
        @endforeach
    </ul>
@else
    <p>No hay usuarios</p>
@endif