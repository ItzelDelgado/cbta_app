<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = ['denominacion_generica',
    'nombre_comercial',
    'descripcion',
    'precio_ml',
    'presentacion_ml',
    'category_id',
    'input_id',];

    //Relacion uno a muchos inversa
    public function category(){
        return $this->belongsTo(Category::class);
    }

    //Relacion uno a muchos inversa
    public function input(){
        return $this->belongsTo(Input::class);
    }
}

