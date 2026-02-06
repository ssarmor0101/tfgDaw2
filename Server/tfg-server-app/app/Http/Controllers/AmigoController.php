<?php

namespace App\Http\Controllers;

use App\Models\Amigo;
use App\Models\User;
use App\Http\Requests\StoreAmigoRequest;
use App\Http\Requests\UpdateAmigoRequest;

class AmigoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $amigos = Amigo::orderBy('user_id')->orderBy('friend_id')->with(['user','friend'])->get();
        return view('amigos.index', compact('amigos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('amigos.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAmigoRequest $request)
    {
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
        return view('amigos.show', compact('amigo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Amigo $amigo)
    {
        $users = User::orderBy('name')->get();
        return view('amigos.edit', compact('amigo','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAmigoRequest $request, Amigo $amigo)
    {
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
        $amigo->delete();
        return redirect()->route('amigos.index');
    }
}
