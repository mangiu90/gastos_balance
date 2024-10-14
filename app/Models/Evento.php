<?php

namespace App\Models;

use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evento extends Model
{
    use HasFactory, AsSource, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'finalizado',
    ];

    /**
     * Name of columns to which http filter can be applied
     *
     * @var array
     */
    protected $allowedFilters = [
        'nombre',
        'fecha_inicio',
        'finalizado',
    ];

    /**
     * Name of columns to which http sorting can be applied
     *
     * @var array
     */
    protected $allowedSorts = [
        'nombre',
        'fecha_inicio',
        'finalizado',
    ];


    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function gastos()
    {
        return $this->movimientos()
            ->selectRaw("sum(case when tipo = '" . Movimiento::EGRESO . "' then monto * -1 else monto end) as saldo")
            ->first()['saldo'];
    }

    public function gastosPorUsuario()
    {
        $count = $this->users()->count();
        if ($count == 0) {
            return 0;
        }
        return $this->gastos() / $count;
    }

    public function usuarioPertenece($user_id)
    {
        return in_array($user_id, $this->users->pluck('id')->all());
    }

    function minimizarTransferencias()
    {
        $deudores = [];
        $acreedores = [];
        $saldos = [];

        foreach ($this->users as $user) {
            $saldos[$user->id] = $user->balancePorEvento($this->id);
        }

        // Separar deudores y acreedores
        foreach ($saldos as $userId => $saldo) {
            $usuario = User::find($userId); // Obtener el usuario por su ID

            if ($saldo < 0) {
                $deudores[] = ['usuario' => $usuario, 'monto' => -$saldo];
            } elseif ($saldo > 0) {
                $acreedores[] = ['usuario' => $usuario, 'monto' => $saldo];
            }
        }

        $transferencias = [];

        // Emparejar deudores con acreedores
        $i = 0;
        $j = 0;
        while ($i < count($deudores) && $j < count($acreedores)) {
            $monto = min($deudores[$i]['monto'], $acreedores[$j]['monto']);

            $transferencias[] = [
                'deudor_id' => $deudores[$i]['usuario']->id,
                'deudor_nombre' => $deudores[$i]['usuario']->name, // Obtener el nombre del deudor
                'acreedor_id' => $acreedores[$j]['usuario']->id,
                'acreedor_nombre' => $acreedores[$j]['usuario']->name, // Obtener el nombre del acreedor
                'monto' => round($monto, 2)
            ];

            $deudores[$i]['monto'] -= $monto;
            $acreedores[$j]['monto'] -= $monto;

            if ($deudores[$i]['monto'] == 0) {
                $i++;
            }
            if ($acreedores[$j]['monto'] == 0) {
                $j++;
            }
        }

        return $transferencias;
    }
}
