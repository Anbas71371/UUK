<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $table = 'status';

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class);
    }
}
