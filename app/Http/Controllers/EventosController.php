<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Evento;
use App\Models\Movimiento;
use App\Http\Requests\NuevoGastoRequest;
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
            'tipo_options' => Movimiento::TIPO_OPTIONS,
        ]);
    }

    public function unirse(Evento $evento)
    {
        $evento->users()->syncWithoutDetaching(auth()->id());
    }

    public function nuevoGasto(Evento $evento, NuevoGastoRequest $request)
    {
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

    public function crearEvento(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'unique:eventos']
        ]);

        Evento::create([
            'nombre' => $request->nombre,
        ]);
    }

    public function detalle(Evento $evento, Request $request)
    {
        return Inertia::render('EventoDetalle', [
            'evento' => $evento,
            'gastos' => number_format($evento->gastos(), 2),
            'gastos_usuario' => number_format($evento->gastosPorUsuario(), 2),
            'movimientos' => $evento->movimientos()
                ->with('user')
                ->orderBy('id', 'desc')
                ->paginate()
                ->withQueryString()
                ->through(fn ($movimiento) => [
                    'id' => $movimiento->id,
                    'fecha' => $movimiento->fecha,
                    'monto' => $movimiento->monto,
                    'monto_format' => number_format($movimiento->monto, 2),
                    'tipo' => $movimiento->tipo,
                    'detalle' => $movimiento->detalle,
                    'user_id' => $movimiento->user_id,
                    'user_name' => $movimiento->user->name,
                ]),
        ]);
    }
}
