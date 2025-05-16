<?php

namespace App\Models\Nutricionales;

use App\Models\User;
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

    public function solicitud_aprobada()
    {
        return $this->hasOne(SolicitudAprobada::class);
    }

}
