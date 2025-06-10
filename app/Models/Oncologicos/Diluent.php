<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diluent extends Model {
    public function medicines()
    {
        return $this->belongsToMany(MedicinesCatalog::class, 'diluent_medicine_catalog', 'diluent_id', 'medicine_catalog_id');
    }
}
