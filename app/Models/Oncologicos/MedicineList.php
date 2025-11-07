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
    public function chargeByMg(): bool { return $this->charge_by === 'mg'; }
    public function chargeByFrasco(): bool { return $this->charge_by === 'frasco'; }
}
