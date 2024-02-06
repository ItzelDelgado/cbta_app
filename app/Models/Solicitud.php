<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'solicitud_detail_id',
        'solicitud_patient_id',
        'is_active',
    ];

    //Relacion uno a uno inversa
    public function solicitud_detail(){
        return $this->belongsTo(SolicitudDetail::class);
    }

    //Relacion uno a uno inversa
    public function solicitud_patient(){
        return $this->belongsTo(SolicitudPatient::class);
    }
}
