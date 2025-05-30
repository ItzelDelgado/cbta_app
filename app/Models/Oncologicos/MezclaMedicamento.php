<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MezclaMedicamento extends Model {
    public function mezcla() {
        return $this->belongsTo(Mezcla::class, 'mezcla_id');
    }

    public function medicamentoOnco() {
        return $this->belongsTo(MedicineOnco::class, 'medicamento_id');
    }

    public function diluyente() {
        return $this->belongsTo(Diluent::class, 'diluyente_id');
    }

    public function viaAdministracion() {
        return $this->belongsTo(AdministrationRoute::class, 'via_administracion_id');
    }
}
