<?php

namespace App\Models\Oncologicos;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudOnco extends Model {
    public function mezclas() {
        return $this->hasMany(Mezcla::class, 'solicitud_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
