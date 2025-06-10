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

}
