@if(!empty($puntuaciones))
    <ul>
        @foreach($puntuaciones as $puntuacion)
            <li>
                {{ $puntuacion->user->name }} -
                {{ $puntuacion->juego->name }} -
                {{ $puntuacion->puntuacion }} -
                <a href="{{ route('puntuaciones.show', $puntuacion) }}">Ver</a> -
                <a href="{{ route('puntuaciones.edit', $puntuacion) }}">Editar</a> -
                <form action="{{ route('puntuaciones.destroy', $puntuacion) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button>Eliminar</button>
                </form>
            </li>
        @endforeach
    </ul>
@else
    <p>No hay puntuaciones</p>
@endif