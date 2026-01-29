<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLogroRequest;
use App\Http\Requests\UpdateLogroRequest;
use App\Models\Juego;
use App\Models\Logro;
use Illuminate\Http\Request;

class LogroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logros = Logro::all();
        return view('logros.index', compact('logros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $juegos = Juego::all();
        return view('logros.create', compact('juegos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLogroRequest $request)
    {
        $validate = $request->validated();

        Logro::create($validate);

        return redirect(route('logros.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Logro $logro)
    {
        $juegos = Juego::all();
        return view('logros.show', compact('logro', 'juegos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Logro $logro)
    {
        $juegos = Juego::all();
        return view('logros.edit', compact('logro', 'juegos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLogroRequest $request, Logro $logro)
    {
        $validate = $request->validated();

        $logro->update($validate);

        return redirect(route('logros.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logro $logro)
    {
        $logro->delete();
        return redirect(route('logros.index'));
    }

    public function restore(Logro $logro) {
        $logro->restore();
        return redirect(route('logros.index'));
    }
}
