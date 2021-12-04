<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificaciones extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    //relaciones
    public function apoderado()
    {
        return $this->belongsTo('App\Models\Apoderado', 'idapoderado');
    }

    public function estudiante()
    {
        return $this->belongsTo('App\Models\Estudiantes', 'idEstudiante');
    }
}
