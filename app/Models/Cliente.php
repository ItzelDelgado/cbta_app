<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'apellido',
    ];

    public function hospitals()
    {
        return $this->belongsToMany(\App\Models\Hospital::class, 'cliente_hospital');
    }
}
