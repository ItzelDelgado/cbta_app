<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspeccionMezcla extends Model {
    use HasFactory;

    protected $fillable = [
        'mezcla_id',
        'es_limpia',
        'es_libre',
        'fecha_inspeccion',
        'hora_inspeccion',
        'tipo_contenedor',
        'tipo_contenedor_otro',
        'esta_rotulado',
        'numero_lote',
        'medicamento',
        'dosis_volumen_total',
        'volumen_medicamento',
        'rubrica_preparador',
        'sello_seguridad',
        'presenta_grietas',
        'presenta_fugas',
        'esta_roto',
        'coloracion_apropiada',
        'contenido_homogeneo',
        'presenta_particulas',
        'presenta_turbidez',
        'volumen_correcto',
        'aprueba_contenido',
        'aprueba_contenedor',
        'dosis_volumen',
        'peso_mezcla',
        'mezcla_aprobada',
        'observaciones',
        'reviso_nombre',
        'aprobo_nombre',
    ];
    public function mezcla() {
        return $this->belongsTo(Mezcla::class, 'mezcla_id');
    }
}
