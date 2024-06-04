<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends AuthenticatableUser
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'nama',  'password',  'type',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

   
    // }

  
    
    public function getTypeAttribute($value)
    {
        return ["petugas", "administrator"][$value]; // Ubah nilai 'type' menjadi deskriptif saat diambil
    }
}

