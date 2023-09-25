<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    public function centers(){
        return $this->hasMany(Center::class, 'hospital_id','id');
    }

    public function leads(){
        return $this->hasMany(Lead::class, 'hospital_id', 'id');
    }

    public function users(){
        return $this->hasMany(User::class, 'hospital_id','id');
    }
}
