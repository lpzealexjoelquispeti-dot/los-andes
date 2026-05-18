<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Tienda;


// 1. IMPORTAMOS EL TRAIT DE SPATIE AQUÍ ARRIBA
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
use SoftDeletes;
    // 2. ACTIVAMOS EL TRAIT DE SPATIE AQUÍ ADENTRO
    use HasRoles;
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'ap_pat',
        'ap_mat',
        'email',
        'password',
        'foto_perfil',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // Relación: Un usuario puede tener una (o varias) tiendas
    // En App\Models\User.php
public function tiendas()
{
    return $this->hasMany(Tienda::class, 'cod_usuario_tie','id');
    // Ajusta los foreign keys según tu migración
}
}