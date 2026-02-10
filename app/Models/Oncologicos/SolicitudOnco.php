<?php

namespace App\Models\Oncologicos;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudOnco extends Model
{

    protected $table = 'solicitud_oncos'; // si es necesario

    protected $casts = [
        'fecha_entrega' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'hospital_id', // 👈 FALTABA
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
        'alergias',
        'fecha_entrega',
        'observaciones',
        'nombre_medico',
        'cedula_medico',
        'estado',
        'remision',
    ];

    public function mezclas()
    {
        return $this->hasMany(Mezcla::class, 'solicitud_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
