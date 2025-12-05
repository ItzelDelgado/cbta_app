<?php

namespace App\Models\Oncologicos;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MedicineList extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'active_brands',
        'charge_by', // ⬅️ nuevo
    ];

    protected $casts = [
        'active_brands' => 'boolean',
        'charge_by'     => 'string', // 'mg' | 'frasco'
    ];

    public function medicines()
    {
        return $this->belongsToMany(
            MedicineOnco::class,
            'medicine_medicine_lists',
            'medicine_list_id',
            'medicine_id'
        )->withPivot('precio')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helpers opcionales
    public function chargeByMg(): bool
    {
        return $this->charge_by === 'mg';
    }
    public function chargeByFrasco(): bool
    {
        return $this->charge_by === 'frasco';
    }

    public function presentations()
    {
        return $this->belongsToMany(
            MedicinePresentation::class,
            'medicine_list_presentation',          // tabla pivot
            'medicine_list_id',                    // FK a esta tabla
            'medicine_presentation_id'             // FK a presentations
        )->withPivot([
            'charge_by',
            'precio',
            'precio_mg_override',
        ])->withTimestamps();
    }
}
