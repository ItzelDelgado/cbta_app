<?php

namespace App\Models\Oncologicos;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineList extends Model {

    protected $fillable = [
        'user_id',
        'name',
        'description'
    ];

    public function medicines() {
        return $this->belongsToMany(MedicineOnco::class, 'medicine_medicine_lists', 'medicine_list_id', 'medicine_id')
        ->withPivot('precio')
        ->withTimestamps();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
