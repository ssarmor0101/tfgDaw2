<?php

namespace App\Http\Controllers;

use App\Models\Amigo;
use App\Models\User;
use App\Http\Requests\StoreAmigoRequest;
use App\Http\Requests\UpdateAmigoRequest;
use Illuminate\Support\Facades\Auth;

class AmigoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Amigo::class);

        $user = Auth::user();
        $amigos = [];

        if ($user->isAdmin()) {
            $amigos = Amigo::with(['user','friend'])->paginate($this->paginatesNumber);
        } else {
            $amigos = Amigo::with(['user','friend'])->where('user_id', $user->id)->orWhere('friend_id', $user->id)->paginate($this->paginatesNumber);
        }

        $extraData = [
            'createButton' => $user->isAdmin(),
            'actionButtons' => $user->isAdmin()
        ];

        return view('amigos.index', compact('amigos', 'extraData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Amigo::class);
        $users = User::orderBy('name')->get();
        return view('amigos.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAmigoRequest $request)
    {
        $this->authorize('create', Amigo::class);
        // handled by StoreAmigoRequest
        /** @var \App\Http\Requests\StoreAmigoRequest $request */
        $data = $request->validated();
        if ($data['user_id'] > $data['friend_id']) {
            [$data['user_id'], $data['friend_id']] = [$data['friend_id'], $data['user_id']];
        }
        Amigo::create($data);
        return redirect()->route('amigos.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Amigo $amigo)
    {
        $this->authorize('view', $amigo);
        $users = User::all();
        return view('amigos.show', compact('amigo', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Amigo $amigo)
    {
        $this->authorize('update', $amigo);
        $users = User::orderBy('name')->get();
        return view('amigos.edit', compact('amigo','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAmigoRequest $request, Amigo $amigo)
    {
        $this->authorize('update', $amigo);
        /** @var \App\Http\Requests\UpdateAmigoRequest $request */
        $data = $request->validated();
        if ($data['user_id'] > $data['friend_id']) {
            [$data['user_id'], $data['friend_id']] = [$data['friend_id'], $data['user_id']];
        }
        $amigo->update($data);
        return redirect()->route('amigos.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Amigo $amigo)
    {
        $this->authorize('create', $amigo);
        $amigo->delete();
        return redirect()->route('amigos.index');
    }
}
