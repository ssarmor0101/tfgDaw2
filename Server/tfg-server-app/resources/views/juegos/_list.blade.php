@if(!empty($juegos))
    <ul>
        @foreach($juegos as $juego)
            <li>
                {{ $juego->name }} -
                {{ $juego->description }} -
                <a href="{{ route('juegos.show', $juego) }}">Ver</a> -
                <a href="{{ route('juegos.edit', $juego) }}">Editar</a> -
                <form action="{{ route('juegos.destroy', $juego) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button>Eliminar</button>
                </form>
            </li>
        @endforeach
    </ul>
@else
    <p>No hay juegos</p>
@endif