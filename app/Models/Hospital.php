<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'adress',
    'is_active',];

    //Relacion uno a muchos, por que un hospital puede ser asignado a varios usuarios
    public function users(){
        return $this->hasMany(User::class);
    }

}
