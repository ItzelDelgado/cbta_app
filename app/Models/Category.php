<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name',];

    //Relacion uno a muchos, por que un hospital puede ser asignado a varias medicinas
    public function medicines(){
        return $this->hasMany(Medicine::class);
    }
}
