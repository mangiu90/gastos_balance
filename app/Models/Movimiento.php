<?php

namespace App\Models;

use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movimiento extends Model
{
    use HasFactory, AsSource, Filterable;

    public const INGRESO = 'INGRESO';
    public const EGRESO = 'EGRESO';

    public const TIPO_OPTIONS = [
        self::INGRESO => 'Gasto',
        self::EGRESO => 'Retiro',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'evento_id',
        'fecha',
        'monto',
        'tipo',
        'detalle',
    ];

    /**
     * Name of columns to which http filter can be applied
     *
     * @var array
     */
    protected $allowedFilters = [
        'user_id',
        'evento_id',
        'fecha',
        'monto',
        'tipo',
        'detalle',
    ];

    /**
     * Name of columns to which http sorting can be applied
     *
     * @var array
     */
    protected $allowedSorts = [
        'user_id',
        'evento_id',
        'fecha',
        'monto',
        'tipo',
        'detalle',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
