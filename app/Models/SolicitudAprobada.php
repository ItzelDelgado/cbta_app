<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudAprobada extends Model
{
    use HasFactory;

    protected $fillable = [
        'lote',
        'solicitud_id',
        'fecha_hora_preparacion',
        'fecha_hora_limite_uso',
        'fecha_hora_envio',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
        
    }


}
