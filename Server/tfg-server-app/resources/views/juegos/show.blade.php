<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juegos</title>
</head>
<body>
    <h1>Ver Juego</h1>

    <label>Nombre</label>
    <input type="text" name="name" value="{{ $juego->name }}" disabled/>

    <br/><br/>

    <label>Descripcion</label>
    <input type="text" name="description" value="{{ $juego->description }}" disabled/>

    <br/><br/>

    <a href="{{ route('juegos.index') }}">Volver</a>
</body>
</html>