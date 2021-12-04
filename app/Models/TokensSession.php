<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokensSession extends Model
{
    use HasFactory;
    protected $table = 'tokenSession';

    //relaciones
    public function apoderado()
    {
        return $this->belongsTo('App\Models\Apoderado', 'idUser');
    }
}
