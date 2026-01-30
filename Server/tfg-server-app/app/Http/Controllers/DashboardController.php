<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use App\Models\Logro;
use App\Models\User;

class DashboardController extends Controller
{
    public function index() {
        $juegos = Juego::all()->sortByDesc('updated_at')->slice(0,3);
        $logros = Logro::all()->sortByDesc('updated_at')->slice(0,3);
        $users = User::all()->sortByDesc('updated_at')->slice(0,3);

        return view('dashboard.index', compact('juegos', 'logros', 'users'));
    }
}
