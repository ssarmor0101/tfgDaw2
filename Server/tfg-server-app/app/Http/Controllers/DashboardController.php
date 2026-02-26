<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use App\Models\Logro;
use App\Models\User;
use App\Models\Puntuacion;
use App\Models\Resultado;
use App\Models\Amigo;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index() {
        $user = Auth::user();

        $juegos = Juego::all()->sortByDesc('updated_at')->slice(0,3);
        $logros = Logro::all()->sortByDesc('updated_at')->slice(0,3);

        $users = [];
        $puntuaciones = [];
        $resultados = [];
        $amigos = [];

        if ($user->isAdmin()) {
            $puntuaciones = Puntuacion::all()->sortByDesc('updated_at')->slice(0,3);
            $resultados = Resultado::all()->sortByDesc('updated_at')->slice(0,3);
            $amigos = Amigo::all()->sortByDesc('updated_at')->slice(0,3);
            $users = User::all()->sortByDesc('updated_at')->slice(0,3);
        } else {
            $puntuaciones = Puntuacion::where('user_id', $user->id)->get()->sortByDesc('updated_at')->slice(0,3);
            $resultados = Resultado::where('user_id', $user->id)->get()->sortByDesc('updated_at')->slice(0,3);
            $amigos = Amigo::where('user_id', $user->id)->orWhere('friend_id', $user->id)->get()->sortByDesc('updated_at')->slice(0,3);
        }
        

        $extraData = [
            'actionButtons' => $user->isAdmin()
        ];

        return view('dashboard.index', compact('juegos', 'logros', 'users', 'puntuaciones', 'resultados', 'amigos', 'extraData'));
    }
}
