<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diluent extends Model {
    public function mezclaMedicamentos() {
        return $this->hasMany(MezclaMedicamento::class, 'diluyente_id');
    }

    public function medicineDiluents() {
        return $this->hasMany(MedicineDiluent::class);
    }
}
