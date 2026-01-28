<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <form method='POST' action="{{ route('login') }}">
    @csrf

    @error('fail')
      <div>{{ $message }}</div>
    @enderror

    <input type='email' name='email' placeholder='Email'>
    <input type='password' name='password' placeholder='Clave'>
    <button type='submit'>Entrar</button>
  </form>
</body>
</html>