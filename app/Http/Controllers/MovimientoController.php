<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function editar(Movimiento $movimiento, Request $request)
    {
        $request->validate([
            'monto' => ['required', 'numeric', 'gt:0', 'max:9999999999999999'],
            'detalle' => ['nullable', 'max:255'],
        ]);

        $movimiento->monto = $request->monto;
        $movimiento->detalle = $request->detalle;
        $movimiento->save();
    }

    public function eliminar(Movimiento $movimiento)
    {
        $movimiento->delete();
    }
}
