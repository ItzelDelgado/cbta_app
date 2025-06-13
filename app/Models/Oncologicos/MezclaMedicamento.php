<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MezclaMedicamento extends Model {

    protected $fillable = [
    'mezcla_id',
    'medicamento_id',
    'nombre_medicamento',
    'dosis',
    'precio_unitario',
];
    protected $table = 'mezcla_medicamentos'; // si es necesario
    use HasFactory;

    public function mezcla() {
        return $this->belongsTo(Mezcla::class, 'mezcla_id');
    }

    public function medicamentoOnco() {
        return $this->belongsTo(MedicineOnco::class, 'medicamento_id');
    }

}
