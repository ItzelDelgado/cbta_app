<?php

namespace App\Models\Oncologicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspeccionMezcla extends Model {
    public function mezcla() {
        return $this->belongsTo(Mezcla::class, 'mezcla_id');
    }
}
