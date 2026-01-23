<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juegos</title>
</head>
<body>
    <h1>Crear Juego</h1>
    <form action="{{ route('juegos.store') }}" method="POST">
        @csrf

        <label>Nombre</label>
        <input type="text" name="name"/>

        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <label>Descripcion</label>
        <input type="text" name="description"/>

        @error('description')
            <div class="error">{{ $message }}</div>
        @enderror

        <br/><br/>

        <button type="submit">Enviar</button>

        <br/><br/>

    </form>

    <a href="{{ route('juegos.index') }}">Volver</a>
</body>
</html>