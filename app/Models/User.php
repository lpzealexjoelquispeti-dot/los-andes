<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'ap_pat',
        'ap_mat',
        'email',
        'password',
        'foto_perfil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tiendas()
    {
        return $this->hasMany(Tienda::class, 'cod_usuario_tie', 'id');
    }

    public function alquileres()
    {
        return $this->hasMany(Alquiler::class, 'cod_usuario_cli', 'id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'cod_usuario_not', 'id');
    }

    public function valoraciones()
    {
        return $this->hasMany(Valoracion::class, 'cod_usuario_val', 'id');
    }
}
