<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DiluentMedicineCatalog extends Pivot
{
    protected $table = 'diluent_medicine_catalog';

    protected $fillable = [
        'diluent_id',
        'medicine_catalog_id',
    ];

    public $timestamps = false;


}
