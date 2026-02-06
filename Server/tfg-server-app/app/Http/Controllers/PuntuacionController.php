<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePuntuacionRequest;
use App\Http\Requests\UpdatePuntuacionRequest;
use App\Models\Juego;
use App\Models\Puntuacion;
use App\Models\User;
use Illuminate\Http\Request;

class PuntuacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $puntuaciones = Puntuacion::all()->sortByDesc(['updated_at', 'puntuacion']);
        return view('puntuaciones.index', compact('puntuaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $juegos = Juego::all();
        return view('puntuaciones.create', compact('users', 'juegos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePuntuacionRequest $request)
    {
        $validate = $request->validated();
        Puntuacion::create($validate);
        return redirect(route('puntuaciones.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Puntuacion $puntuacion)
    {
        $users = User::all();
        $juegos = Juego::all();
        return view('puntuaciones.show', compact('users', 'juegos', 'puntuacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Puntuacion $puntuacion)
    {
        $users = User::all();
        $juegos = Juego::all();
        return view('puntuaciones.edit', compact('users', 'juegos', 'puntuacion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePuntuacionRequest $request, Puntuacion $puntuacion)
    {
        $validate = $request->validated();
        $puntuacion->update($validate);
        return redirect(route('puntuaciones.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Puntuacion $puntuacion)
    {
        $puntuacion->delete();
        return redirect(route('puntuaciones.index'));
    }
}
