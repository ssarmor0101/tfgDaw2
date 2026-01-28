<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJuegoRequest;
use App\Http\Requests\UpdateJuegoRequest;
use App\Models\Juego;
use Illuminate\Http\Request;

class JuegoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $juegos = Juego::all();

        return view('juegos.index', compact('juegos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('juegos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJuegoRequest $request)
    {
        $validate = $request->validated();

        Juego::create($validate);

        return redirect(route('juegos.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Juego $juego)
    {
        return view('juegos.show', compact('juego'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Juego $juego)
    {
        return view('juegos.edit', compact('juego'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJuegoRequest $request, Juego $juego)
    {
        $validate = $request->validated();

        $juego->update($validate);

        return redirect(route('juegos.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Juego $juego)
    {
        // Juego::onlyTrashed()->get();


        $juego->delete();
        return redirect(route('juegos.index'));
    }

    public function restore(Juego $juego) {
        $juego->restore();
        return redirect(route('juegos.index'));
    }
}
