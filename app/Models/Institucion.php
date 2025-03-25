<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $fillable = [
        'nombre',
    ];
    public function trasladoSecundarioPropios()
    {
        return $this->hasMany(TrasladoSecundarioPropios::class);
    }
}
