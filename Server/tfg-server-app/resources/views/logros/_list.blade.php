@if(!empty($logros))
    <ul>
        @foreach($logros as $logro)
            <li>
                {{ $logro->name }} -
                {{ $logro->description }} -
                {{ $logro->juego->name }} -
                {{ $logro->rareza() }}
                <a href="{{ route('logros.show', $logro) }}">Ver</a> -
                <a href="{{ route('logros.edit', $logro) }}">Editar</a> -
                <form action="{{ route('logros.destroy', $logro) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button>Eliminar</button>
                </form>
            </li>
        @endforeach
    </ul>
@else
    <p>No hay logros</p>
@endif