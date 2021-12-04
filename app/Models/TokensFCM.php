<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokensFCM extends Model
{
    use HasFactory;

    protected $table = 'tokensFCM';

    //relaciones
    public function apoderado()
    {
        return $this->belongsTo('App\Models\Apoderado', 'idUser');
    }
}
