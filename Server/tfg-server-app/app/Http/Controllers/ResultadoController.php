<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResultadoRequest;
use App\Http\Requests\UpdateResultadoRequest;
use App\Models\Logro;
use App\Models\Resultado;
use App\Models\User;
use Illuminate\Http\Request;

class ResultadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resultados = Resultado::all()->sortByDesc('updated_at');
        return view('resultados.index', compact('resultados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $logros = Logro::all();
        return view('resultados.create', compact('users', 'logros'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResultadoRequest $request)
    {
        $validate = $request->validated();

        Resultado::create($validate);

        return redirect(route('resultados.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Resultado $resultado)
    {
        $users = User::all();
        $logros = Logro::all();
        return view('resultados.show', compact('users', 'logros', 'resultado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resultado $resultado)
    {
        $users = User::all();
        $logros = Logro::all();
        return view('resultados.edit', compact('users', 'logros', 'resultado'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResultadoRequest $request, Resultado $resultado)
    {
        $validate = $request->validated();

        $resultado->update($validate);

        return redirect(route('resultados.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resultado $resultado)
    {
        $resultado->delete();
        return redirect(route('resultados.index'));
    }
}
