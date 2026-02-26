<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePuntuacionRequest;
use App\Http\Requests\UpdatePuntuacionRequest;
use App\Models\Juego;
use App\Models\Puntuacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PuntuacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Puntuacion::class);
        $user = Auth::user();
        $puntuaciones = [];

        if($user->isAdmin()) {
            $puntuaciones = Puntuacion::orderByDesc('updated_at', 'puntuacion')->paginate($this->paginatesNumber);
        } else {
            $puntuaciones = Puntuacion::where('user_id', $user->id)->orderByDesc('updated_at', 'puntuacion')->paginate($this->paginatesNumber);
        }

        $extraData = [
            'createButton' => $user->isAdmin(),
            'actionButtons' => $user->isAdmin()
        ];

        return view('puntuaciones.index', compact('puntuaciones', 'extraData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Puntuacion::class);
        $users = User::all();
        $juegos = Juego::all();
        return view('puntuaciones.create', compact('users', 'juegos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePuntuacionRequest $request)
    {
        $this->authorize('create', Puntuacion::class);
        $validate = $request->validated();
        Puntuacion::create($validate);
        return redirect(route('puntuaciones.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Puntuacion $puntuacion)
    {
        $this->authorize('view', $puntuacion);
        $users = User::all();
        $juegos = Juego::all();
        return view('puntuaciones.show', compact('users', 'juegos', 'puntuacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Puntuacion $puntuacion)
    {
        $this->authorize('update', $puntuacion);
        $users = User::all();
        $juegos = Juego::all();
        return view('puntuaciones.edit', compact('users', 'juegos', 'puntuacion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePuntuacionRequest $request, Puntuacion $puntuacion)
    {
        $this->authorize('update', $puntuacion);
        $validate = $request->validated();
        $puntuacion->update($validate);
        return redirect(route('puntuaciones.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Puntuacion $puntuacion)
    {
        $this->authorize('delete', $puntuacion);
        $puntuacion->delete();
        return redirect(route('puntuaciones.index'));
    }
}
