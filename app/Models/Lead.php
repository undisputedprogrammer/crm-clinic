<?php

namespace App\Models;

use App\Models\User;
use App\Models\Remark;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = ['name','phone','email','city','is_valid','is_genuine','history','customer_segment','status','followup_created'];

    public function remarks(){
        return $this->morphMany(Remark::class,'remarkable');
    }

    public function followups(){
        return $this->hasMany(Followup::class, 'lead_id')->orderBy('created_at');
    }

    public function answers(){
        return $this->hasMany(Answer::class, 'lead_id');
    }

    public function assigned(){
        return $this->hasOne(User::class,'assigned_id','id');
    }

    public function appointment(){
        return $this->hasOne(Appointment::class, 'lead_id', 'id');
    }
}
