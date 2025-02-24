<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISSListado extends Model
{
    protected $table = 'isslistados';
    protected $fillable = ['nombre'];
}
