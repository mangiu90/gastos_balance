<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Inertia\Inertia;
use Illuminate\Http\Request;

class EventosController extends Controller
{
    public function index()
    {
        $eventos = [];

        Evento::all()->map(function ($evento) use (&$eventos) {
            $ev['id'] = $evento->id;
            $ev['nombre'] = $evento->nombre;
            $ev['balance'] = number_format($evento->balance(), 2);
            $ev['balance_usuario'] = number_format($evento->balancePorUsuario(), 2);
            $ev['usuario_pertenece'] = $evento->usuarioPertenece(auth()->id());
            $ev['users'] = $evento->users->transform(function ($user) use ($evento) {
                return [
                    'name' => $user->name,
                    'balance' => number_format($user->balancePorEvento($evento->id), 2),
                    'saldo' => number_format($user->saldoPorEvento($evento->id), 2),
                ];
            });

            $eventos[] = $ev;
        });

        return Inertia::render('Eventos', [
            'eventos' => $eventos,
        ]);
    }

    public function unirse(Evento $evento)
    {
        $evento->users()->syncWithoutDetaching(auth()->id());
    }

    public function addMovimiento(Request $request)
    {
        dd($request->all());
    }
}
