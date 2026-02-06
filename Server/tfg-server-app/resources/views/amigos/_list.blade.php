@if(isset($amigos) && $amigos->count())
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario A</th>
                <th>Usuario B</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($amigos as $amigo)
                <tr>
                    <td>{{ $amigo->id }}</td>
                    <td>{{ $amigo->user->name ?? '—' }} ({{ $amigo->user_id }})</td>
                    <td>{{ $amigo->friend->name ?? '—' }} ({{ $amigo->friend_id }})</td>
                    <td>
                        <a href="{{ route('amigos.show', $amigo) }}">Ver</a>
                        |
                        <a href="{{ route('amigos.edit', $amigo) }}">Editar</a>
                        |
                        <form action="{{ route('amigos.destroy', $amigo) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Eliminar amistad?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No hay amistades registradas.</p>
@endif