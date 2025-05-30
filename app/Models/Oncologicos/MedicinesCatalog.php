<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicinesCatalog extends Model
{
    use HasFactory;

    protected $table = 'medicines_catalog';

    protected $fillable = ['denominacion', 'presentacion', 'state'];

    // Una medicina del catálogo puede estar en muchas configuraciones con precio personalizado
    public function medicineOncos()
    {
        return $this->hasMany(MedicineOnco::class, 'catalog_id');
    }
}
