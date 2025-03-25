<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambulancias extends Model
{
    protected $fillable = [
        'unidad',
        'placa',
    ];
    public function trasladoSecundario()
    {
        return $this->hasMany(TrasladoSecundario::class);
    }
    public function trasladoSecundarioGestor()
    {
        return $this->hasMany(TrasladoSecundarioGestores::class);
    }
    public function trasladoSecundarioHistorico()
    {
        return $this->hasMany(TrasladoSecundarioHistorico::class);
    }
    public function trasladoSecundarioPropio()
    {
        return $this->hasMany(TrasladoSecundarioPropios::class);
    }
}
