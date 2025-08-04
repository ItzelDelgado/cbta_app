<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicinesCatalog extends Model
{
    use HasFactory;

    protected $table = 'medicines_catalog';

    protected $fillable = ['denominacion',
                            'presentacion',
                            'state',
                            'cantidad_medicamento',
                            'volumen_diluyente',
                            'conc_min',
                            'conc_max',
                            'legend',];

    // Una medicina del catálogo puede estar en muchas configuraciones con precio personalizado
    public function medicineOncos()
    {
        return $this->hasMany(MedicineOnco::class, 'catalog_id');
    }

    public function diluents()
    {
        return $this->belongsToMany(Diluent::class, 'diluent_medicine_catalog', 'medicine_catalog_id', 'diluent_id');
    }

    public function administrationRoutes()
    {
        return $this->belongsToMany(AdministrationRoute::class, 'administration_route_medicine_catalog', 'medicine_catalog_id', 'administration_route_id');
    }

}
