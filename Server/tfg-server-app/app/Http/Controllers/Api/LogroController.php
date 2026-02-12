<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogroRequest;
use App\Http\Resources\LogroResource;
use App\Models\Logro;
use Illuminate\Http\Request;

class LogroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $logros = Logro::all();
        $logros = Logro::with('juego')->with('resultados')->get();
        // $logros = Logro::paginate(5);
        // return $logros;
        // return $logros->toJson();
        return response()->json($logros, 200);
        // return response()->json(LogroResource::collection($logros), 200); // Faltan metadatos
        return LogroResource::collection($logros);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLogroRequest $request)
    {
        $validate = $request->validated();

        $logro = Logro::create($validate);

        // return $logro->toJson();
        return response()->json(['message' => 'Logro creado con exito'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Logro $logro)
    {
        return response()->json($logro, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Logro $logro)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logro $logro)
    {
        //
    }
}
