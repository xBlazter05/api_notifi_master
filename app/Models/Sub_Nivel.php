<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub_Nivel extends Model
{
    use HasFactory;
    protected $table = 'sub_nivel';

    //relaciones
    public function subNivel()
    {
        return $this->belongsTo('App\Models\Niveles', 'idNivel');
    }
}
