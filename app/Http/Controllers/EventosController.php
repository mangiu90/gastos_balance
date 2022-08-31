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
            $ev['gastos'] = number_format($evento->gastos(), 2);
            $ev['gastos_usuario'] = number_format($evento->gastosPorUsuario(), 2);
            $ev['usuario_pertenece'] = $evento->usuarioPertenece(auth()->id());
            $ev['users'] = $evento->users->transform(function ($user) use ($evento) {
                $balance = $user->balancePorEvento($evento->id);
                return [
                    'name' => $user->name,
                    'gastos' => number_format($user->gastosPorEvento($evento->id), 2),
                    'balance' => number_format($balance, 2),
                    'color' => $balance < 0 ? 'red' : 'green',
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
