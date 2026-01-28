<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $juegos = Juego::all()->sortByDesc('updated_at')->slice(0,3);

        return view('dashboard.index', compact('juegos'));
    }
}
