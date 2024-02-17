<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudPatient extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre_paciente',
        'servicio',
        'cama',
        'piso',
        'registro',
        'diagnostico',
        'edad',
        'peso',
        'sexo',
        'fecha_nacimiento'
    ];

    //Relacion uno a uno
    public function solicitud()
    {
        return $this->hasOne(Solicitud::class);
    }
}
