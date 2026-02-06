<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicacion</title>
</head>
<body>
    <header style="display: flex; gap: 30px;">
        <nav style="display: flex; gap: 10px;">
            <a href="{{ route('dashboard.index') }}">Dashboard</a>
            <a href="{{ route('juegos.index') }}">Juegos</a>
            <a href="{{ route('logros.index') }}">Logros</a>
            <a href="{{ route('resultados.index') }}">Resultados</a>
            <a href="{{ route('puntuaciones.index') }}">Puntuaciones</a>
            <a href="{{ route('amigos.index') }}">Amigos</a>
            <a href="{{ route('users.index') }}">Usuarios</a>
        </nav>
        <a href="{{ route('logout') }}">Logout</a>
    </header>
    <main>
        @hasSection('content')
            @yield('content')
        @endif
    </main>
    <footer></footer>
</body>
</html>