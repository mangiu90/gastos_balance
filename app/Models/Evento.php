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

    public function balance()
    {
        return $this->movimientos()
            ->selectRaw("sum(case when tipo = '" . Movimiento::EGRESO . "' then monto * -1 else monto end) as saldo")
            ->first()['saldo'];
    }

    public function balancePorUsuario()
    {
        $count = $this->users()->count();
        if ($count == 0) {
            return 0;
        }
        return $this->balance() / $count;
    }

    public function usuarioPertenece($user_id)
    {
        return in_array($user_id, $this->users->pluck('id')->all());
    }
}
