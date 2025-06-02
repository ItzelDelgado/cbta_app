<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Nutricionales\Solicitud;
use App\Models\Oncologicos\MedicineList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'password',
        'username',
        'is_active',
        'hospital_id',
        'medicine_list_id', // 👈 esto es clave
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    // protected $appends = [
    //     'profile_photo_url',
    // ];
    //Relacion uno a uno inversa, por que solo esta asignado a un hospital
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    //Relacion uno a uno
    public function solicitud()
    {
        return $this->hasOne(Solicitud::class);
    }

    public function medicineLists()
    {
        return $this->hasMany(MedicineList::class);
    }

    public function assignedMedicineList()
    {
        return $this->belongsTo(MedicineList::class, 'medicine_list_id');
    }
}
