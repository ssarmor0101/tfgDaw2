<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juegos</title>
</head>
<body>
    
    <h2>Juegos</h2>
    <a href="{{ route('juegos.create') }}">Crear tarea</a>
    
    @include('juegos._list')

</body>
</html>