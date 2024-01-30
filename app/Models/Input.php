<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    use HasFactory;

    protected $fillable = ['description',
    'is_active','tipo_input', 'orden_enum',];

    //Relacion uno a muchos, por que una category puede ser asignado a varias medicinas
    public function medicines(){
        return $this->hasMany(Medicine::class);
    }
}
