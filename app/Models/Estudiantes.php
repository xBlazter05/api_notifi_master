<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiantes extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';
    //relaciones
    public function apoderado()
    {
        return $this->belongsTo('App\Models\Apoderado', 'idapoderado');
    }

    public function subNivel()
    {
        return $this->belongsTo('App\Models\Sub_Nivel', 'idSubNivel');
    }

    protected $hidden = [
        'password'
    ];

}
