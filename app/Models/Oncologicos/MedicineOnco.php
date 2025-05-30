<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineOnco extends Model {
    protected $table = 'medicine_oncos';

    protected $fillable = ['catalog_id', 'precio'];

    public function mezclaMedicamentos() {
        return $this->hasMany(MezclaMedicamento::class, 'medicamento_id');
    }

    public function medicineLists() {
        return $this->belongsToMany(MedicineList::class, 'medicine_medicine_lists', 'medicine_id', 'medicine_list_id')
        ->withPivot('precio')
        ->withTimestamps();
    }

    public function diluents() {
        return $this->belongsToMany(Diluent::class, 'medicine_diluents', 'medicine_id', 'diluent_id');
    }

    public function routes() {
        return $this->belongsToMany(AdministrationRoute::class, 'medicine_routes', 'medicine_id', 'route_id');
    }

    public function catalog()
    {
        return $this->belongsTo(MedicinesCatalog::class, 'catalog_id');
    }
}
