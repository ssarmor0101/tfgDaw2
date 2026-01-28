@extends('layout')

@section('content')
    <h1>Dashboard</h1>

    <h2>Juegos</h2>
    @include('juegos._list')
@endsection