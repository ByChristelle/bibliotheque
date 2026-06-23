<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory,SoftDeletes, Notifiable;

    /**
     * Les attributs transférables en masse.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar',
        'role',
        'status',
        'last_login_at',
    ];
   
    protected $dates = ['deleted_at'];

    /**
     * Les attributs masqués pour la sérialisation (API).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs à transtyper.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];
}