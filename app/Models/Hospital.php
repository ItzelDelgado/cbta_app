<?php

namespace App\Models;

use App\Models\Oncologicos\Laboratory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'adress',
        'laboratory_id', // ✅ NUEVO
        'is_active',
    ];

    //Relacion uno a muchos, por que un hospital puede ser asignado a varios usuarios
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function clientes()
    {
        return $this->belongsToMany(\App\Models\Cliente::class, 'cliente_hospital');
    }

    public function medicineList()
    {
        return $this->hasOne(\App\Models\Oncologicos\MedicineList::class, 'hospital_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }
}
