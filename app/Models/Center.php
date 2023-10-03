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

    public function users(){
        return $this->belongsToMany(User::class, 'user_has_centers');
    }

    public function doctors(){
        return $this->hasMany(Doctor::class, 'center_id','id');
    }

    public function agents()
    {
        $arr = [];
        foreach ($this->users as $u) {
            if($u->hasRole('agent')){
                $arr[] = $u;
            }
        }
        return $arr;
    }

}
