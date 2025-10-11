<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mezcla extends Model
{
    use HasFactory;

    protected $table = 'mezclas';

    protected $fillable = [
        'solicitud_id',
        'volumen_dilucion',
        'tiempo_infusion',
        'estado',
        'remision',
        'lote',
        'infusor_id', // ✅ nuevos
        'set_infusion',
    ];
    protected $casts = ['set_infusion' => 'boolean'];


    public function solicitud()
    {
        return $this->belongsTo(SolicitudOnco::class, 'solicitud_id');
    }

    public function medicamentos()
    {
        return $this->hasMany(MezclaMedicamento::class, 'mezcla_id');
    }

    public function inspeccion()
    {
        return $this->hasOne(InspeccionMezcla::class, 'mezcla_id');
    }

    // ✅ Relación con infusor
    public function infusor()
    {
        return $this->belongsTo(Infusor::class, 'infusor_id');
    }
}
