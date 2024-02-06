<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_id',
        'lote',
        'caducidad',
        'input_ml',
        'solicitud_id',
    ];

    //Relacion uno a uno inversa
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }
}
