<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Evento;
use App\Models\Movimiento;
use App\Http\Requests\NuevoGastoRequest;

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
            'tipo_options' => Movimiento::TIPO_OPTIONS,
        ]);
    }

    public function unirse(Evento $evento)
    {
        $evento->users()->syncWithoutDetaching(auth()->id());
    }

    public function nuevoGasto(Evento $evento, NuevoGastoRequest $request)
    {
        // dd($evento, $request->all());
        $eventoPropio = auth()->user()->eventoPropio($evento->id);

        if (isset($eventoPropio)) {
            $data['user_id'] = auth()->id();
            $data['evento_id'] = $evento->id;
            $data['fecha'] = now();
            $data['tipo'] = $request->tipo;
            $data['monto'] = $request->monto;
            $data['detalle'] = $request->detalle;
            Movimiento::create($data);
        }
    }
}
