<?php
// app/Models/Oncologicos/DiluentPresentation.php
namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Model;

class DiluentPresentation extends Model
{
    protected $fillable = [
        'diluent_id',
        'presentacion',             // ej. "Bolsa 500 mL"
        'volume_ml',                // ej. 500.00
        'denominacion_comercial',   // ej. "NaCl 0.9% B. Braun"
        'lote',
        'caducidad',
        'is_active',
    ];

    protected $casts = [
        'volume_ml' => 'float',
        'is_active' => 'boolean',
        'caducidad' => 'date',
    ];

    public function diluent()
    {
        return $this->belongsTo(Diluent::class);
    }
}
