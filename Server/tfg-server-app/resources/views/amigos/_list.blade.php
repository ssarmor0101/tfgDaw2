@if(isset($amigos) && $amigos->count())
    <ul>
        @foreach($amigos as $amigo)
            <li>
                {{ $amigo->id }}
                {{ $amigo->user->name ?? '—' }} ({{ $amigo->user_id }}) -
                <td>{{ $amigo->friend->name ?? '—' }} ({{ $amigo->friend_id }}) -
                <a href="{{ route('amigos.show', $amigo) }}">Ver</a>
                @if(isset($extraData['actionButtons']) && $extraData['actionButtons'])
                        -
                    <a href="{{ route('amigos.edit', $amigo) }}">Editar</a> -
                    <form action="{{ route('amigos.destroy', $amigo) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Eliminar amistad?')">Eliminar</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
@else
    <p>No hay amistades registradas.</p>
@endif