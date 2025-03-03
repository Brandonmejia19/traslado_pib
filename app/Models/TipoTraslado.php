<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoTraslado extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function trasladossecundarios(): HasMany
    {
        return $this->hasMany(TrasladoSecundario::class);
    }
    public function trasladossecundariospropios(): HasMany
    {
        return $this->hasMany(TrasladoSecundarioPropios::class);
    }
}
