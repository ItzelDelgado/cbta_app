<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_id',
        'lote',
        'caducidad',
        'valor',
        'valor_sobrellenado',
        'valor_ml',
        'solicitud_id',
    ];

    //Relacion uno a muchos inversa
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }
}
