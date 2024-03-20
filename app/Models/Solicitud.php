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
        'is_aprobada'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relacion uno a uno inversa
    public function solicitud_detail()
    {
        return $this->belongsTo(SolicitudDetail::class);
    }

    //Relacion uno a uno inversa
    public function solicitud_patient()
    {
        return $this->belongsTo(SolicitudPatient::class);
    }

    public function input()
    {
        return $this->hasMany(SolicitudInput::class);
    }


    // public function getInputValue($inputId)
    // {
    //     $detail = $this->input->firstWhere('id', $inputId);
    //     if ($detail) {
    //         return $detail->value;
    //     }

    //     return null;
    // }

    
}
