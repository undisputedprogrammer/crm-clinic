<?php

namespace App\Models;

use App\Models\Remark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    public function remarks(){
        return $this->morphMany(Remark::class,'remarkable')->orderBy('created_at')->select('id','remark');
    }

    public function followups(){
        return $this->hasMany(Followup::class, 'lead_id')->orderBy('created_at');
    }
}
