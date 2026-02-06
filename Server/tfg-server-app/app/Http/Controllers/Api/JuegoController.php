<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJuegoRequest;
use App\Models\Juego;
use App\Services\JuegoService;
use Illuminate\Http\Request;

class JuegoController extends Controller
{
    protected $juegoService;

    public function __construct(JuegoService $juegoService)
    {
        $this->juegoService = $juegoService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $juegos = Juego::all();

        return $juegos->toJson();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJuegoRequest $request)
    {
        $validate = $request->validated();

        //$juego = Juego::create($validate);
        $juego = $this->juegoService->storeJuego($validate);

        // return $juego->toJson();
        return response()->json(['message' => 'Juego creado', 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Juego $juego)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Juego $juego)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Juego $juego)
    {
        //
    }

    public function restore(Juego $juego) {
        $bool = $juego->restore();
        return $bool;
    }
}
