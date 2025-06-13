<?php

namespace App\Models\Oncologicos;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudOnco extends Model {

    protected $table = 'solicitud_oncos'; // si es necesario

    protected $fillable = [
        'user_id',
        'servicio',
        'nombre_paciente',
        'sexo',
        'edad',
        'peso',
        'cama',
        'piso',
        'registro_paciente',
        'fecha_nacimiento',
        'diagnostico',
        'fecha_solicitud',
        'horario_entrega',
        'observaciones',
        'nombre_medico',
        'cedula_medico',
        'estado',
        'remision',
    ];

    public function mezclas() {
        return $this->hasMany(Mezcla::class, 'solicitud_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
