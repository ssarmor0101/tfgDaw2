<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJuegoRequest;
use App\Http\Requests\UpdateJuegoRequest;
use App\Models\Juego;
use App\Services\JuegoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->authorize('viewAny', Juego::class);

        $user = Auth::user();

        $juegos = Juego::orderByDesc('updated_at')->paginate($this->paginatesNumber);

        $extraData = [
            'createButton' => $user->isAdmin(),
            'actionButtons' => $user->isAdmin()
        ];

        return view('juegos.index', compact('juegos', 'extraData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Juego::class);
        return view('juegos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJuegoRequest $request)
    {
        $this->authorize('create', Juego::class);
        $validate = $request->validated();

        // Juego::create($validate);
        $this->juegoService->storeJuego($validate);

        return redirect(route('juegos.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Juego $juego)
    {
        $this->authorize('view', $juego);
        return view('juegos.show', compact('juego'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Juego $juego)
    {
        $this->authorize('update', $juego);
        return view('juegos.edit', compact('juego'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJuegoRequest $request, Juego $juego)
    {
        $this->authorize('update', $juego);
        $validate = $request->validated();

        $juego->update($validate);

        return redirect(route('juegos.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Juego $juego)
    {
        $this->authorize('delete', $juego);
        // Juego::onlyTrashed()->get();


        $juego->delete();
        return redirect(route('juegos.index'));
    }

    public function restore(Juego $juego) {
        $this->authorize('restore', $juego);
        $juego->restore();
        return redirect(route('juegos.index'));
    }
}
