<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResultadoRequest;
use App\Http\Requests\UpdateResultadoRequest;
use App\Models\Logro;
use App\Models\Resultado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Resultado::class);
        $user = Auth::user();
        $resultados = [];

        if ($user->isAdmin()) {
            $resultados = Resultado::orderByDesc('updated_at')->paginate($this->paginatesNumber);
        } else {
            $resultados = Resultado::where('user_id', $user->id)->orderByDesc('updated_at')->paginate($this->paginatesNumber);
        }
        
        $extraData = [
            'createButton' => $user->isAdmin(),
            'actionButtons' => $user->isAdmin()
        ];
        return view('resultados.index', compact('resultados', 'extraData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Resultado::class);
        $users = User::all();
        $logros = Logro::all();
        return view('resultados.create', compact('users', 'logros'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResultadoRequest $request)
    {
        $this->authorize('create', Resultado::class);
        $validate = $request->validated();

        Resultado::create($validate);

        return redirect(route('resultados.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Resultado $resultado)
    {
        $this->authorize('view', $resultado);
        $users = User::all();
        $logros = Logro::all();
        return view('resultados.show', compact('users', 'logros', 'resultado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resultado $resultado)
    {
        $this->authorize('update', $resultado);
        $users = User::all();
        $logros = Logro::all();
        return view('resultados.edit', compact('users', 'logros', 'resultado'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResultadoRequest $request, Resultado $resultado)
    {
        $this->authorize('update', $resultado);
        $validate = $request->validated();

        $resultado->update($validate);

        return redirect(route('resultados.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resultado $resultado)
    {
        $this->authorize('delete', $resultado);
        $resultado->delete();
        return redirect(route('resultados.index'));
    }
}
