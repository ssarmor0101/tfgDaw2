<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogroRequest;
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
        return $logros->toJson();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLogroRequest $request)
    {
        $validate = $request->validated();

        $logro = Logro::create($validate);

        return $logro->toJson();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
