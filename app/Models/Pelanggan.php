<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',  'alamat',  'no_telepon',
    ];
    protected $primaryKey = 'pelangganid'; // Ganti dengan nama kolom ID yang sesuai

}
