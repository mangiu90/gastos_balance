<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Orchid\Platform\Models\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions'          => 'array',
        'email_verified_at'    => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function eventos()
    {
        return $this->belongsToMany(Evento::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }


    public function isAdmin()
    {
        return $this->hasAccess('platform.systems.users');
    }

    public function eventoPropio($evento_id)
    {
        return $this->eventos()->where('evento_id', $evento_id)->first();
    }

    public function gastosPorEvento($evento_id)
    {
        return $this->movimientos()
            ->where('evento_id', $evento_id)
            ->selectRaw("sum(case when tipo = '" . Movimiento::EGRESO . "' then monto * -1 else monto end) as saldo")
            ->first()['saldo'];
    }

    public function balancePorEvento($evento_id)
    {
        return $this->gastosPorEvento($evento_id) - $this->eventoPropio($evento_id)->gastosPorUsuario();
    }
}
