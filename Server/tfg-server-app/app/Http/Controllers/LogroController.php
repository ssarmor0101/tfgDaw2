<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLogroRequest;
use App\Http\Requests\UpdateLogroRequest;
use App\Models\Juego;
use App\Models\Logro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Logro::class);
        
        $user = Auth::user();

        $logros = Logro::orderByDesc('updated_at')->paginate($this->paginatesNumber);

        $extraData = [
            'createButton' => $user->isAdmin(),
            'actionButtons' => $user->isAdmin()
        ];
        
        return view('logros.index', compact('logros', 'extraData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Logro::class);
        $juegos = Juego::all();
        return view('logros.create', compact('juegos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLogroRequest $request)
    {
        $this->authorize('create', Logro::class);
        $validate = $request->validated();

        Logro::create($validate);

        return redirect(route('logros.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Logro $logro)
    {
        $this->authorize('view', $logro);
        $juegos = Juego::all();
        return view('logros.show', compact('logro', 'juegos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Logro $logro)
    {
        $this->authorize('update', $logro);
        $juegos = Juego::all();
        return view('logros.edit', compact('logro', 'juegos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLogroRequest $request, Logro $logro)
    {
        $this->authorize('update', $logro);
        $validate = $request->validated();

        $logro->update($validate);

        return redirect(route('logros.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logro $logro)
    {
        $this->authorize('delete', $logro);
        $logro->delete();
        return redirect(route('logros.index'));
    }

    public function restore(Logro $logro) {
        $this->authorize('restore', $logro);
        $logro->restore();
        return redirect(route('logros.index'));
    }
}
