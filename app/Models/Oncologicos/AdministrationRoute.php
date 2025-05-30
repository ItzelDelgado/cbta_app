<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrationRoute extends Model {
    public function mezclaMedicamentos() {
        return $this->hasMany(MezclaMedicamento::class, 'via_administracion_id');
    }
}
