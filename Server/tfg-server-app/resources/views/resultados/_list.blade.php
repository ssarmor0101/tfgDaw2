@if(!empty($resultados))
    <ul>
        @foreach($resultados as $resultado)
            <li>
                {{ $resultado->user->name }} -
                {{ $resultado->logro->name }} -
                <a href="{{ route('resultados.show', $resultado) }}">Ver</a> -
                <a href="{{ route('resultados.edit', $resultado) }}">Editar</a> -
                <form action="{{ route('resultados.destroy', $resultado) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button>Eliminar</button>
                </form>
            </li>
        @endforeach
    </ul>
@else
    <p>No hay resultados</p>
@endif