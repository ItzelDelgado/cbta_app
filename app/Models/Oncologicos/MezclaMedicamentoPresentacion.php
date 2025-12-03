<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MezclaMedicamentoPresentacion extends Model
{
    use HasFactory;

    protected $table = 'mezcla_medicamento_presentaciones';

    protected $fillable = [
        'mezcla_medicamento_id',
        'medicine_batch_id',
        'unidades_usadas',
        'lote_usado',
        'caducidad_usada',
        'precio_frasco_snapshot',
        'subtotal',
    ];

    protected $casts = [
        'unidades_usadas'       => 'integer',
        'caducidad_usada'       => 'date',
        'precio_frasco_snapshot' => 'decimal:4',
        'subtotal'              => 'decimal:4',
    ];

    public function mezclaMedicamento()
    {
        return $this->belongsTo(MezclaMedicamento::class, 'mezcla_medicamento_id');
    }

    public function batch()
    {
        return $this->belongsTo(MedicineBatch::class, 'medicine_batch_id');
    }

    // Acceso rápido a la presentación (vía batch)
    public function presentation()
    {
        return $this->hasOneThrough(
            MedicinePresentation::class,
            MedicineBatch::class,
            'id',                        // local key on MedicineBatch
            'id',                        // local key on MedicinePresentation
            'medicine_batch_id',         // foreign key on this model
            'medicine_presentation_id'   // foreign key on MedicineBatch
        );
    }
}
