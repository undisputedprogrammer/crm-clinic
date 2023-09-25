<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    use HasFactory;

    public function hospital(){
        return $this->hasOne(Hospital::class, 'hospital_id', 'id');
    }

    public function agents(){
        return $this->belongsToMany(User::class, 'user_has_centers');
    }
}
