<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// 6. Mezcla
class Mezcla extends Model {
    public function solicitud() {
        return $this->belongsTo(SolicitudOnco::class, 'solicitud_id');
    }

    public function medicamentos() {
        return $this->hasMany(MezclaMedicamento::class, 'mezcla_id');
    }

    public function inspeccion() {
        return $this->hasOne(InspeccionMezcla::class, 'mezcla_id');
    }
}
