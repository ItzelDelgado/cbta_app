<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineBatch extends Model
{
    use HasFactory;

    protected $table = 'medicine_batches';

    protected $fillable = [
        'medicine_presentation_id',
        'lote',
        'caducidad',
        'is_current',
    ];

    protected $casts = [
        'caducidad' => 'date',
        'is_current' => 'boolean',
    ];

    public function presentation()
    {
        return $this->belongsTo(MedicinePresentation::class, 'medicine_presentation_id');
    }

    // Scope auxiliar: solo vigentes
    public function scopeCurrent($q)
    {
        return $q->where('is_current', true);
    }
}
